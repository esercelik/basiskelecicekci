<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Requests\Admin\UpdateProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\ProductImageManager;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $filters = $request->only(['search', 'category', 'status', 'stock', 'featured']);
        $filters['category'] = ctype_digit((string) ($filters['category'] ?? '')) ? (string) $filters['category'] : '';
        $products = Product::query()
            ->with(['category:id,name', 'primaryImage:id,product_id,image_path,alt_text', 'firstImage:id,product_id,image_path,alt_text'])
            ->when(filled($filters['search'] ?? null), function (Builder $query) use ($filters): void {
                $query->where(function (Builder $searchQuery) use ($filters): void {
                    $searchQuery->where('name', 'like', '%'.$filters['search'].'%')
                        ->orWhere('sku', 'like', '%'.$filters['search'].'%');
                });
            })
            ->when(filled($filters['category'] ?? null), fn (Builder $query): Builder => $query->where('category_id', $filters['category']))
            ->when(in_array($filters['status'] ?? null, ['active', 'inactive'], true), fn (Builder $query): Builder => $query->where('is_active', $filters['status'] === 'active'))
            ->when(in_array($filters['stock'] ?? null, [Product::STOCK_STATUS_IN_STOCK, Product::STOCK_STATUS_PRE_ORDER, Product::STOCK_STATUS_OUT_OF_STOCK], true), fn (Builder $query): Builder => $query->where('stock_status', $filters['stock']))
            ->when(in_array($filters['featured'] ?? null, ['yes', 'no'], true), fn (Builder $query): Builder => $query->where('is_featured', $filters['featured'] === 'yes'))
            ->ordered()
            ->paginate(15)
            ->withQueryString();
        $categories = Category::query()->ordered()->get(['id', 'name']);

        return view('admin.products.index', compact('products', 'categories', 'filters'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.products.create', ['product' => new Product, 'categories' => Category::query()->ordered()->get(['id', 'name'])]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request, ProductImageManager $imageManager): RedirectResponse
    {
        $data = $request->safe()->except(['images']);
        $files = $request->file('images', []);

        $product = DB::transaction(function () use ($data, $files, $imageManager): Product {
            $product = Product::query()->create($data);
            $imageManager->addUploads($product, $files);

            return $product;
        });

        return to_route('admin.products.edit', $product)->with('status', 'Ürün oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): View
    {
        $product->load(['category:id,name', 'images']);

        return view('admin.products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product): View
    {
        $product->load('images');
        $categories = Category::query()->ordered()->get(['id', 'name']);

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product, ProductImageManager $imageManager): RedirectResponse
    {
        $data = $request->safe()->except(['images', 'image_alts', 'image_order', 'primary_image_id']);
        $files = $request->file('images', []);

        DB::transaction(function () use ($product, $data, $files, $request, $imageManager): void {
            $product->update($data);
            $imageManager->updateMetadata($product, $request->input('image_alts', []), $request->input('image_order', []), $request->integer('primary_image_id') ?: null);
            $imageManager->addUploads($product, $files);
        });

        return to_route('admin.products.edit', $product)->with('status', 'Ürün güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product, ProductImageManager $imageManager): RedirectResponse
    {
        $paths = $imageManager->managedPaths($product);

        DB::transaction(fn (): ?bool => $product->delete());
        $imageManager->deleteManagedPaths($paths);

        return to_route('admin.products.index')->with('status', 'Ürün silindi.');
    }
}
