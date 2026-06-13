<?php

namespace App\Services;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Http;
use Throwable;

class AiSuggestionService
{
    public function suggestFor(Product $product): array
    {
        return $this->postToAi([
            'task' => 'product_suggestions',
            'product' => $this->productPayload($product),
        ], [
            'summary' => 'AI suggestions are temporarily unavailable.',
            'titles' => [],
            'descriptions' => [],
            'seo' => [],
            'tags' => [],
        ]);
    }

    public function productCopyDraft(array $context): array
    {
        $response = $this->postToAi([
            'task' => 'product_copy',
            'context' => $context,
            'expected_response' => [
                'title' => 'string',
                'description' => 'string',
                'seo_title' => 'string',
                'seo_description' => 'string',
                'seo_keywords' => 'comma separated string',
                'tags' => ['string'],
            ],
        ], []);

        return [
            'title' => $response['title'] ?? $response['name'] ?? $context['name'] ?? '',
            'description' => $response['description'] ?? '',
            'seo_title' => $response['seo_title'] ?? data_get($response, 'seo.title') ?? '',
            'seo_description' => $response['seo_description'] ?? data_get($response, 'seo.description') ?? '',
            'seo_keywords' => $response['seo_keywords'] ?? data_get($response, 'seo.keywords') ?? '',
            'tags' => $response['tags'] ?? [],
            'raw' => $response,
        ];
    }

    public function recommendations(?User $user = null, array $context = [], int $limit = 8): EloquentCollection
    {
        $available = Product::available()
            ->with(['vendorStore', 'images', 'category'])
            ->latest()
            ->take(24)
            ->get();

        $response = $this->postToAi([
            'task' => 'recommend_products',
            'user' => $user ? [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'wishlist_product_ids' => $user->wishlistItems()->pluck('product_id')->all(),
            ] : null,
            'context' => $context,
            'catalog' => $available->map(fn (Product $product) => $this->productPayload($product))->values()->all(),
            'limit' => $limit,
        ], []);

        $ids = collect($response['product_ids'] ?? $response['ids'] ?? [])
            ->filter()
            ->map(fn ($id) => (int) $id)
            ->all();

        $slugs = collect($response['slugs'] ?? [])
            ->filter()
            ->values()
            ->all();

        collect($response['recommendations'] ?? [])->each(function ($recommendation) use (&$ids, &$slugs) {
            if (is_array($recommendation)) {
                if (isset($recommendation['id'])) {
                    $ids[] = (int) $recommendation['id'];
                }

                if (isset($recommendation['slug'])) {
                    $slugs[] = $recommendation['slug'];
                }
            }
        });

        $query = Product::available()->with(['vendorStore', 'images', 'category']);

        if ($ids || $slugs) {
            return $query
                ->where(function ($query) use ($ids, $slugs) {
                    $query
                        ->when($ids, fn ($query) => $query->whereIn('id', $ids))
                        ->when($slugs, fn ($query) => $query->orWhereIn('slug', $slugs));
                })
                ->take($limit)
                ->get();
        }

        return $this->fallbackRecommendations($context, $limit);
    }

    private function fallbackRecommendations(array $context, int $limit): EloquentCollection
    {
        return Product::available()
            ->with(['vendorStore', 'images', 'category'])
            ->when($context['category_id'] ?? null, fn ($query, $categoryId) => $query->where('category_id', $categoryId))
            ->when($context['exclude_product_id'] ?? null, fn ($query, $productId) => $query->whereKeyNot($productId))
            ->latest()
            ->take($limit)
            ->get();
    }

    private function postToAi(array $payload, array $fallback): array
    {
        try {
            $response = Http::timeout((int) config('services.ai_suggestions.timeout', 5))
                ->acceptJson()
                ->post(config('services.ai_suggestions.url'), $payload);

            if (! $response->successful()) {
                return $fallback;
            }

            return $response->json() ?? $fallback;
        } catch (Throwable) {
            return $fallback;
        }
    }

    private function productPayload(Product $product): array
    {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'slug' => $product->slug,
            'description' => $product->description,
            'category' => $product->category?->name,
            'category_id' => $product->category_id,
            'vendor' => $product->vendorStore?->name,
            'price_cents' => $product->price_cents,
            'stock' => $product->stock,
            'seo_title' => $product->seo_title,
            'seo_description' => $product->seo_description,
            'seo_keywords' => $product->seo_keywords,
        ];
    }
}
