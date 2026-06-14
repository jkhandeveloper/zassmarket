<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class AiService
{
    public function health(): array
    {
        try {
            $response = Http::timeout($this->timeout())
                ->acceptJson()
                ->get($this->url('/health'));

            if (! $response->successful()) {
                Log::error('AI service health check failed.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return $this->failureResponse('AI service is temporarily unavailable.');
            }

            return $response->json() ?? ['ok' => true];
        } catch (Throwable $exception) {
            Log::error('AI service health check exception.', [
                'message' => $exception->getMessage(),
                'exception' => $exception::class,
            ]);

            return $this->failureResponse('AI service is temporarily unavailable.');
        }
    }

    public function analyzeProductImage(UploadedFile $image): array
    {
        try {
            $response = Http::timeout($this->timeout())
                ->acceptJson()
                ->attach(
                    'file',
                    fopen($image->getRealPath(), 'r'),
                    $image->getClientOriginalName()
                )
                ->post($this->url('/analyze-product-image'));

            if (! $response->successful()) {
                Log::error('AI product image analysis failed.', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return $this->fallbackAnalysis();
            }

            return $this->normalizeAnalysis($response->json() ?? []);
        } catch (Throwable $exception) {
            Log::error('AI product image analysis exception.', [
                'message' => $exception->getMessage(),
                'exception' => $exception::class,
            ]);

            return $this->fallbackAnalysis();
        }
    }

    private function normalizeAnalysis(array $payload): array
    {
        return [
            'ok' => true,
            'message' => $payload['message'] ?? null,
            'product_name' => $payload['product_name'] ?? $payload['name'] ?? '',
            'description' => $payload['description'] ?? '',
            'seo_title' => $payload['seo_title'] ?? data_get($payload, 'seo.title', ''),
            'meta_description' => $payload['meta_description'] ?? $payload['seo_description'] ?? data_get($payload, 'seo.description', ''),
            'tags' => $this->normalizeTags($payload['tags'] ?? []),
            'raw' => $payload,
        ];
    }

    private function fallbackAnalysis(): array
    {
        return $this->failureResponse('AI image analysis is temporarily unavailable. Please try again later.');
    }

    private function failureResponse(string $message): array
    {
        return [
            'ok' => false,
            'message' => $message,
            'product_name' => '',
            'description' => '',
            'seo_title' => '',
            'meta_description' => '',
            'tags' => [],
            'raw' => [],
        ];
    }

    private function normalizeTags(mixed $tags): array
    {
        if (is_string($tags)) {
            return collect(explode(',', $tags))
                ->map(fn (string $tag) => trim($tag))
                ->filter()
                ->values()
                ->all();
        }

        if (is_array($tags)) {
            return collect($tags)
                ->filter(fn ($tag) => is_string($tag) && trim($tag) !== '')
                ->map(fn (string $tag) => trim($tag))
                ->values()
                ->all();
        }

        return [];
    }

    private function url(string $path): string
    {
        return rtrim((string) config('services.ai.url'), '/').'/'.ltrim($path, '/');
    }

    private function timeout(): int
    {
        return (int) config('services.ai.timeout', 10);
    }
}
