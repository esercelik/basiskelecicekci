<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ProductImageManager;
use Illuminate\Http\RedirectResponse;

class ProductImageController extends Controller
{
    public function destroy(Product $product, ProductImage $image, ProductImageManager $imageManager): RedirectResponse
    {
        abort_unless($image->product_id === $product->id, 404);
        $image->load('product');
        $imageManager->delete($image);

        return to_route('admin.products.edit', $product)->with('status', 'Görsel silindi.');
    }
}
