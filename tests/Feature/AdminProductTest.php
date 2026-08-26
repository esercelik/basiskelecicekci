<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class AdminProductTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_create_update_and_filter_products(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $payload = ['category_id' => $category->id, 'name' => 'Zarif Lale', 'slug' => '', 'sku' => 'LT-1', 'short_description' => 'Kısa açıklama', 'description' => '<b>Güvenli</b>', 'price' => 1000, 'sale_price' => 800, 'stock_status' => 'in_stock', 'is_active' => true, 'is_featured' => true, 'sort_order' => 1];

        $this->actingAs($admin)->post(route('admin.products.store'), $payload);
        $product = Product::query()->where('slug', 'zarif-lale')->firstOrFail();
        $this->assertSame('Güvenli', $product->description);
        $this->put(route('admin.products.update', $product), array_merge($payload, ['slug' => 'zarif-lale-2', 'name' => 'Yeni Lale', 'sale_price' => null]))->assertRedirect(route('admin.products.edit', $product->fresh()));
        $this->get(route('admin.products.index', ['search' => 'Yeni', 'category' => $category->id, 'status' => 'active']))->assertOk()->assertSee('Yeni Lale');
    }

    public function test_price_validation_and_public_inactive_visibility_and_authorization_work(): void
    {
        $category = Category::factory()->create();
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin)->from(route('admin.products.create'))->post(route('admin.products.store'), ['category_id' => $category->id, 'name' => 'Hatalı', 'slug' => 'hatali', 'short_description' => 'Test', 'price' => 100, 'sale_price' => 100, 'stock_status' => 'in_stock', 'is_active' => true, 'is_featured' => false, 'sort_order' => 0])->assertSessionHasErrors('sale_price');
        $product = Product::factory()->inactive()->for($category)->create();
        $this->get(route('products.show', $product->slug))->assertNotFound();
        $this->actingAs(User::factory()->create())->delete(route('admin.products.destroy', $product))->assertForbidden();
    }
}
