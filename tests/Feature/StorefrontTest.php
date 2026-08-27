<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\StoreSetting;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_home_page_renders_successfully(): void
    {
        Category::factory()->create(['name' => 'Buketler', 'slug' => 'buketler']);

        $this->get(route('home'))->assertOk()->assertSee('Başiskele ve çevresine');
    }

    public function test_product_listing_page_renders_successfully(): void
    {
        Product::factory()->create(['name' => 'Test Buketi', 'slug' => 'test-buketi']);

        $this->get(route('products.index'))->assertOk()->assertSee('Test Buketi');
    }

    public function test_active_product_detail_page_renders_successfully(): void
    {
        $product = Product::factory()->create(['name' => 'Pembe Lale', 'slug' => 'pembe-lale']);

        $this->get(route('products.show', $product))->assertOk()->assertSee('Pembe Lale');
    }

    public function test_inactive_product_is_not_visible_to_visitors(): void
    {
        $product = Product::factory()->inactive()->create(['slug' => 'gizli-buket']);

        $this->get(route('products.show', $product))->assertNotFound();
    }

    public function test_category_page_shows_only_its_active_products(): void
    {
        $category = Category::factory()->create(['name' => 'Buketler', 'slug' => 'buketler']);
        Product::factory()->for($category)->create(['name' => 'Kategori Buketi', 'slug' => 'kategori-buketi']);
        Product::factory()->for($category)->inactive()->create(['name' => 'Pasif Buket', 'slug' => 'pasif-buket']);
        Product::factory()->create(['name' => 'Diğer Kategori Ürünü', 'slug' => 'diger-kategori-urunu']);

        $this->get(route('categories.show', $category))->assertOk()->assertSee('Kategori Buketi')->assertDontSee('Pasif Buket')->assertDontSee('Diğer Kategori Ürünü');
    }

    public function test_whatsapp_order_link_contains_product_name_and_url(): void
    {
        $product = Product::factory()->create(['name' => 'Zarif Gül', 'slug' => 'zarif-gul']);
        StoreSetting::query()->create([
            'id' => 1,
            'name' => 'Başiskele Çiçek',
            'address' => 'Başiskele',
            'whatsapp_number' => '0555 111 11 11',
        ]);

        $this->get(route('products.show', $product))->assertOk()->assertSee('https://wa.me/905551111111?text=')->assertSee(rawurlencode('Zarif Gül'))->assertSee(rawurlencode(route('products.show', $product)));
    }

    public function test_sale_price_is_displayed_when_a_product_is_discounted(): void
    {
        $product = Product::factory()->create(['slug' => 'indirimli-buket', 'price' => 1000, 'sale_price' => 800]);

        $this->get(route('products.show', $product))->assertOk()->assertSee($product->formattedCurrentPrice())->assertSee($product->formattedPrice());
    }

    public function test_contact_page_renders_successfully(): void
    {
        $this->get(route('contact'))->assertOk()->assertSee('Bir çiçek kadar yakınız.');
    }

    public function test_out_of_stock_product_shows_information_instead_of_order_button(): void
    {
        $product = Product::factory()->outOfStock()->create(['slug' => 'stokta-yok-buket']);

        $this->get(route('products.show', $product))->assertOk()->assertSee('Bu ürün şu an stokta yok.')->assertDontSee('WhatsApp’tan Sipariş Ver');
    }
}
