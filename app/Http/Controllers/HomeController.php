<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\SubscriptionPlan;
use App\Services\AiSuggestionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(AiSuggestionService $ai): View
    {
        return view('market.home', [
            'featuredProducts' => Product::available()->with(['vendorStore', 'images'])->withAvg('reviews', 'rating')->withCount('reviews')->latest()->take(8)->get(),
            'recommendedProducts' => $ai->recommendations(auth()->user(), ['source' => 'home'], 4),
            'categories' => Category::withCount('products')->take(6)->get(),
            'plans' => SubscriptionPlan::where('is_active', true)->orderBy('price_cents')->get(),
        ]);
    }

    public function products(Request $request): View
    {
        $products = Product::available()
            ->with(['vendorStore', 'images', 'category'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('category'), fn ($query) => $query->whereHas('category', fn ($category) => $category->where('slug', $request->string('category'))))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return view('market.products.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function showProduct(Product $product, AiSuggestionService $ai): View
    {
        abort_unless($product->is_active && $product->vendorStore?->isApproved(), 404);

        $product->load(['vendorStore', 'images', 'category']);
        $product->loadAvg('reviews', 'rating')->loadCount('reviews');

        $sameNameProducts = Product::available()
            ->with(['vendorStore', 'images', 'category'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->whereKeyNot($product->id)
            ->where('name', $product->name)
            ->latest()
            ->take(4)
            ->get();

        $sameCategoryProducts = Product::available()
            ->with(['vendorStore', 'images', 'category'])
            ->withAvg('reviews', 'rating')
            ->withCount('reviews')
            ->whereKeyNot($product->id)
            ->where('category_id', $product->category_id)
            ->latest()
            ->take(4)
            ->get();

        return view('market.products.show', [
            'product' => $product,
            'productReviews' => $product->reviews()->with('user')->latest()->take(10)->get(),
            'sameNameProducts' => $sameNameProducts,
            'sameCategoryProducts' => $sameCategoryProducts,
            'recommendedProducts' => $ai->recommendations(auth()->user(), [
                'source' => 'product_detail',
                'exclude_product_id' => $product->id,
                'category_id' => $product->category_id,
                'product' => [
                    'id' => $product->id,
                    'name' => $product->name,
                    'category' => $product->category?->name,
                ],
            ], 4),
        ]);
    }

    public function page(Page $page): View
    {
        abort_unless($page->is_published, 404);

        return view('market.page', ['page' => $page]);
    }
}
