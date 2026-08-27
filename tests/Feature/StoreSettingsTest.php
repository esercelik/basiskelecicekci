<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\StoreSetting;
use App\Models\User;
use App\Services\StoreSettings;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class StoreSettingsTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_admin_can_update_settings_and_whatsapp_link_uses_them(): void
    {
        $this->actingAs(User::factory()->admin()->create())->put(route('admin.settings.update'), ['name' => 'Yeni Çiçekçi', 'phone' => '+90 262 111 11 11', 'whatsapp_number' => '0555 111 11 11', 'address' => 'Başiskele', 'instagram_url' => 'https://instagram.com/ornek', 'map_url' => 'https://maps.google.com/?q=basisk'])->assertRedirect();
        $this->assertDatabaseHas('store_settings', ['id' => 1, 'name' => 'Yeni Çiçekçi']);
        $product = Product::factory()->create();
        $this->assertStringContainsString('905551111111', $product->whatsappOrderUrl());
        $this->get(route('home'))->assertOk()->assertSee('Yeni Çiçekçi')->assertSee('tel:+902621111111');
        $this->actingAs(User::factory()->create())->put(route('admin.settings.update'), [])->assertForbidden();
    }

    public function test_config_values_are_used_when_no_database_setting_exists(): void
    {
        StoreSetting::query()->delete();
        Cache::forget('store.settings');
        $this->assertSame(config('store.name'), app(StoreSettings::class)->get()->name);
    }

    public function test_missing_contact_details_do_not_generate_links_or_json_ld_values(): void
    {
        config()->set('store.phone', null);
        config()->set('store.whatsapp_number', null);
        config()->set('store.instagram_url', null);
        config()->set('store.map_url', null);
        StoreSetting::query()->delete();
        Cache::forget('store.settings');

        $store = app(StoreSettings::class)->get();

        $this->assertNull($store->phoneUrl);
        $this->assertNull($store->whatsappNumber);
        $this->assertNull(Product::generalWhatsappUrl());
        $this->get(route('home'))
            ->assertOk()
            ->assertDontSee('tel:')
            ->assertDontSee('https://wa.me/')
            ->assertDontSee('"telephone"');
    }
}
