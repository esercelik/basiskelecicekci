<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminProductImageTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_images_are_uploaded_with_one_primary_and_primary_deletion_reassigns_it(): void
    {
        Storage::fake('public');
        $product = Product::factory()->create();
        $admin = User::factory()->admin()->create();
        $payload = ['category_id' => $product->category_id, 'name' => $product->name, 'slug' => $product->slug, 'sku' => $product->sku, 'short_description' => $product->short_description, 'price' => $product->price, 'stock_status' => 'in_stock', 'is_active' => true, 'is_featured' => false, 'sort_order' => 0, 'images' => [UploadedFile::fake()->image('one.jpg'), UploadedFile::fake()->image('two.png')]];

        $this->actingAs($admin)->put(route('admin.products.update', $product), $payload)->assertRedirect();
        $images = ProductImage::query()->where('product_id', $product->id)->get();
        $this->assertCount(2, $images);
        $this->assertCount(1, $images->where('is_primary', true));
        $primary = $images->firstWhere('is_primary', true);
        Storage::disk('public')->assertExists($primary->image_path);
        $this->delete(route('admin.products.images.destroy', [$product, $primary]))->assertRedirect();
        Storage::disk('public')->assertMissing($primary->image_path);
        $this->assertDatabaseHas('product_images', ['product_id' => $product->id, 'is_primary' => true]);
    }

    public function test_invalid_product_image_is_rejected(): void
    {
        $product = Product::factory()->create();
        $this->actingAs(User::factory()->admin()->create())->from(route('admin.products.edit', $product))->put(route('admin.products.update', $product), ['category_id' => $product->category_id, 'name' => $product->name, 'slug' => $product->slug, 'short_description' => $product->short_description, 'price' => $product->price, 'stock_status' => 'in_stock', 'is_active' => true, 'is_featured' => false, 'sort_order' => 0, 'images' => [UploadedFile::fake()->create('bad.svg', 10, 'image/svg+xml')]])->assertSessionHasErrors('images.0');
    }
}
