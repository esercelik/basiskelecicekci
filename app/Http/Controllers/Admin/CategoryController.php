<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $categories = Category::query()->withCount('products')->ordered()->paginate(20);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        return view('admin.categories.create', ['category' => new Category]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $path = null;

        try {
            if ($request->hasFile('image')) {
                $path = $request->file('image')->storePublicly('categories', 'public');
                $data['image_path'] = $path;
            }

            $category = Category::query()->create($data);
        } catch (\Throwable $exception) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }

            throw $exception;
        }

        return to_route('admin.categories.edit', $category)->with('status', 'Kategori oluşturuldu.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category): RedirectResponse
    {
        return to_route('admin.categories.edit', $category);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category): View
    {
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->safe()->except('image');
        $oldPath = $category->image_path;
        $newPath = null;

        try {
            if ($request->hasFile('image')) {
                $newPath = $request->file('image')->storePublicly('categories', 'public');
                $data['image_path'] = $newPath;
            }

            $category->update($data);
        } catch (\Throwable $exception) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }

            throw $exception;
        }

        if ($newPath && Str::startsWith((string) $oldPath, 'categories/')) {
            Storage::disk('public')->delete($oldPath);
        }

        return to_route('admin.categories.edit', $category)->with('status', 'Kategori güncellendi.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category): RedirectResponse
    {
        if ($category->products()->exists()) {
            return back()->withErrors(['category' => 'Bu kategoride ürünler var. Önce ürünleri başka kategoriye taşıyın veya kategoriyi pasife alın.']);
        }

        $imagePath = $category->image_path;
        $category->delete();

        if (Str::startsWith((string) $imagePath, 'categories/')) {
            Storage::disk('public')->delete($imagePath);
        }

        return to_route('admin.categories.index')->with('status', 'Kategori silindi.');
    }
}
