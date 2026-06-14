<?php

namespace App\Http\Controllers\Ai;

use App\Http\Controllers\Controller;
use App\Services\AiService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductImageAnalysisController extends Controller
{
    public function create(): View
    {
        return view('ai.test-product-image');
    }

    public function store(Request $request, AiService $aiService): View
    {
        $validated = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $result = $aiService->analyzeProductImage($validated['image']);

        return view('ai.test-product-image', [
            'result' => $result,
        ]);
    }
}
