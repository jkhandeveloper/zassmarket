<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\SubscriptionPlan;
use App\Models\VendorStore;
use App\Models\VendorSubscription;
use App\Services\AiSuggestionService;
use App\Services\BankTransferDetailsService;
use App\Services\Notifications\MarketplaceEmailService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class VendorDashboardController extends Controller
{
    public function index(): View
    {
        $store = auth()->user()->vendorStore;

        return view('vendor.dashboard', [
            'store' => $store,
            'orders' => $store?->orders()->with('items')->latest()->take(10)->get() ?? collect(),
            'products' => $store?->products()->latest()->take(10)->get() ?? collect(),
        ]);
    }

    public function apply(): View
    {
        return view('vendor.apply', [
            'plans' => SubscriptionPlan::where('is_active', true)->orderBy('price_cents')->get(),
            'store' => auth()->user()->vendorStore,
        ]);
    }

    public function storeApplication(Request $request, BankTransferDetailsService $bankDetails, MarketplaceEmailService $emails): RedirectResponse
    {
        abort_if(auth()->user()->vendorStore, 409, 'You already have a vendor application.');

        $data = $request->validate([
            'subscription_plan_id' => ['required', 'exists:subscription_plans,id'],
            'name' => ['required', 'string', 'max:255'],
            'support_email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:40'],
            'description' => ['nullable', 'string', 'max:2000'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $store = VendorStore::create([
            ...collect($data)->except('logo')->all(),
            'owner_id' => auth()->id(),
            'slug' => Str::slug($data['name']).'-'.Str::lower(Str::random(5)),
            'logo_path' => $this->storePublicImage($request, 'logo', 'vendor-logos'),
        ]);

        $subscription = VendorSubscription::create([
            'vendor_store_id' => $store->id,
            'subscription_plan_id' => $data['subscription_plan_id'],
            'status' => 'active',
            'payment_status' => 'unpaid',
            'starts_at' => now(),
        ]);

        auth()->user()->assignRole('vendor');
        $bankDetails->emailTo(auth()->user()->email, $subscription->plan);
        $emails->vendorRegistered($store);

        return redirect()->route('vendor.billing.index')->with('status', 'Vendor application submitted. Please complete your subscription payment.');
    }

    public function products(): View
    {
        $store = auth()->user()->vendorStore;

        return view('vendor.products.index', [
            'store' => $store,
            'products' => $store->products()->with('category')->latest()->paginate(12),
        ]);
    }

    public function createProduct(): View
    {
        $store = auth()->user()->vendorStore;

        abort_unless($store->canCreateProduct(), 403, 'Your plan product limit has been reached.');

        return view('vendor.products.form', [
            'product' => new Product,
            'categories' => Category::orderBy('name')->get(),
            'action' => route('vendor.products.store'),
            'method' => 'POST',
        ]);
    }

    public function storeProduct(Request $request, MarketplaceEmailService $emails): RedirectResponse
    {
        $store = auth()->user()->vendorStore;

        abort_unless($store->canCreateProduct(), 403, 'Your plan product limit has been reached.');

        $data = $this->validateProduct($request);
        $product = $store->products()->create([
            ...collect($data)->except(['price', 'image', 'images', 'image_path'])->all(),
            'slug' => Str::slug($data['name']).'-'.Str::lower(Str::random(4)),
            'price_cents' => (int) round($data['price'] * 100),
            'discount_percent' => (int) ($data['discount_percent'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->storeProductImages($request, $product, $data);

        $emails->productCreated($product);

        return redirect()->route('vendor.products.index')->with('status', 'Product created.');
    }

    public function editProduct(Product $product): View
    {
        $this->authorizeProduct($product);

        return view('vendor.products.form', [
            'product' => $product,
            'categories' => Category::orderBy('name')->get(),
            'action' => route('vendor.products.update', $product),
            'method' => 'PATCH',
        ]);
    }

    public function updateProduct(Request $request, Product $product): RedirectResponse
    {
        $this->authorizeProduct($product);

        $data = $this->validateProduct($request);
        $product->update([
            ...collect($data)->except(['price', 'image', 'images', 'image_path'])->all(),
            'price_cents' => (int) round($data['price'] * 100),
            'discount_percent' => (int) ($data['discount_percent'] ?? 0),
            'is_active' => $request->boolean('is_active'),
        ]);

        $this->storeProductImages($request, $product, $data);

        return redirect()->route('vendor.products.index')->with('status', 'Product updated.');
    }

    public function aiProductCopy(Request $request, AiSuggestionService $suggestions): RedirectResponse
    {
        $data = $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'audience' => ['nullable', 'string', 'max:255'],
            'keywords' => ['nullable', 'string', 'max:500'],
            'tone' => ['nullable', 'string', 'max:80'],
        ]);

        $category = isset($data['category_id']) ? Category::find($data['category_id']) : null;
        $draft = $suggestions->productCopyDraft([
            ...$data,
            'category' => $category?->name,
            'vendor' => auth()->user()->vendorStore?->name,
        ]);

        return back()
            ->withInput([
                ...$request->except('_token'),
                'name' => $draft['title'] ?: $request->input('name'),
                'description' => $draft['description'] ?: $request->input('description'),
                'seo_title' => $draft['seo_title'],
                'seo_description' => $draft['seo_description'],
                'seo_keywords' => is_array($draft['seo_keywords']) ? implode(', ', $draft['seo_keywords']) : $draft['seo_keywords'],
            ])
            ->with('ai_product_copy', $draft)
            ->with('status', 'AI product copy generated.');
    }

    public function orders(): View
    {
        $store = auth()->user()->vendorStore;

        return view('vendor.orders', [
            'orders' => $store->orders()->with('items')->latest()->paginate(15),
        ]);
    }

    public function aiSuggestions(Product $product, AiSuggestionService $suggestions): RedirectResponse
    {
        $this->authorizeProduct($product);

        $product->update(['ai_suggestions' => $suggestions->suggestFor($product)]);

        return back()->with('status', 'AI suggestions refreshed.');
    }

    private function validateProduct(Request $request): array
    {
        return $request->validate([
            'category_id' => ['nullable', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
            'seo_keywords' => ['nullable', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0.01', 'max:999999'],
            'discount_percent' => ['nullable', 'integer', 'min:0', 'max:95'],
            'stock' => ['required', 'integer', 'min:0', 'max:999999'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'image_path' => ['nullable', 'string', 'max:255'],
        ]);
    }

    private function authorizeProduct(Product $product): void
    {
        abort_unless($product->vendor_store_id === auth()->user()->vendorStore?->id, 403);
    }

    private function storeProductImages(Request $request, Product $product, array $data): void
    {
        $sortOrder = ((int) $product->images()->max('sort_order')) + 1;

        if ($request->hasFile('image')) {
            $product->images()->create([
                'path' => $this->storePublicImage($request, 'image', 'products'),
                'alt_text' => $product->name,
                'sort_order' => $sortOrder++,
            ]);
        }

        foreach ($request->file('images', []) as $image) {
            $path = $image->store('products', 'public');

            $product->images()->create([
                'path' => Storage::disk('public')->url($path),
                'alt_text' => $product->name,
                'sort_order' => $sortOrder++,
            ]);
        }

        if (! empty($data['image_path']) && ! $product->images()->where('path', $data['image_path'])->exists()) {
            $product->images()->create([
                'path' => $data['image_path'],
                'alt_text' => $product->name,
                'sort_order' => $sortOrder,
            ]);
        }
    }

    private function storePublicImage(Request $request, string $field, string $directory): ?string
    {
        if (! $request->hasFile($field)) {
            return null;
        }

        $path = $request->file($field)->store($directory, 'public');

        return Storage::disk('public')->url($path);
    }
}
