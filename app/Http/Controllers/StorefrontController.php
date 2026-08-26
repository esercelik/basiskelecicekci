<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function home(): View
    {
        $categories = Category::query()->active()->ordered()->get(['id', 'name', 'slug', 'description', 'image_path']);
        $featuredProducts = Product::query()
            ->active()
            ->featured()
            ->whereHas('category', fn (Builder $categoryQuery): Builder => $categoryQuery->active())
            ->with(['category:id,name,slug', 'primaryImage:id,product_id,image_path,alt_text'])
            ->ordered()
            ->limit(6)
            ->get();

        return view('home', compact('categories', 'featuredProducts'));
    }

    public function index(Request $request): View
    {
        $selectedCategorySlug = $request->string('kategori')->trim()->toString();
        $categories = Category::query()->active()->ordered()->get(['id', 'name', 'slug']);
        $products = Product::query()
            ->active()
            ->whereHas('category', fn (Builder $categoryQuery): Builder => $categoryQuery->active())
            ->with(['category:id,name,slug', 'primaryImage:id,product_id,image_path,alt_text'])
            ->when($selectedCategorySlug !== '', fn (Builder $productQuery): Builder => $productQuery->whereHas('category', fn (Builder $categoryQuery): Builder => $categoryQuery->where('slug', $selectedCategorySlug)))
            ->ordered()
            ->get();

        return view('products.index', compact('categories', 'products', 'selectedCategorySlug'));
    }

    public function category(Category $category): View
    {
        $products = $category->products()->active()->with(['category:id,name,slug', 'primaryImage:id,product_id,image_path,alt_text'])->ordered()->get();

        return view('categories.show', compact('category', 'products'));
    }

    public function show(Product $product): View
    {
        $product->load(['category:id,name,slug', 'images:id,product_id,image_path,alt_text,is_primary,sort_order']);

        return view('products.show', compact('product'));
    }

    public function contact(): View
    {
        return view('contact');
    }

    public function sitemap(): Response
    {
        $categories = Category::query()->active()->ordered()->get(['slug', 'updated_at']);
        $products = Product::query()->active()->whereHas('category', fn (Builder $categoryQuery): Builder => $categoryQuery->active())->ordered()->get(['slug', 'updated_at']);

        return response()->view('sitemap', compact('categories', 'products'))->header('Content-Type', 'application/xml');
    }
}
