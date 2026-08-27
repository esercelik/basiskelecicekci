<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ViewSmokeTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_public_pages_render_without_server_errors(): void
    {
        $category = Category::factory()->create();
        $product = Product::factory()->for($category)->create();

        $this->get(route('home'))->assertOk();
        $this->get(route('products.index'))->assertOk();
        $this->get(route('categories.show', $category))->assertOk();
        $this->get(route('products.show', $product))->assertOk();
        $this->get(route('contact'))->assertOk();
        $this->get(route('sitemap'))->assertOk();
        $this->get('/bulunmayan-sayfa')->assertNotFound();
    }

    public function test_admin_get_pages_render_without_server_errors(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $product = Product::factory()->for($category)->create();

        $this->get(route('admin.login'))->assertOk();

        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
        $this->get(route('admin.categories.index'))->assertOk();
        $this->get(route('admin.categories.create'))->assertOk();
        $this->get(route('admin.categories.edit', $category))->assertOk();
        $this->get(route('admin.products.index'))->assertOk();
        $this->get(route('admin.products.create'))->assertOk()->assertSee('Yeni ürün');
        $this->get(route('admin.products.show', $product))->assertOk();
        $this->get(route('admin.products.edit', $product))->assertOk()->assertSee('Ürün düzenle');
        $this->get(route('admin.settings.edit'))->assertOk();
        $this->get(route('admin.profile.edit'))->assertOk();
    }
}
