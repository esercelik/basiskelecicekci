<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['Buketler', 'buketler', 'Taze mevsim çiçekleriyle özenle hazırlanan buketler.', 'buketler'],
            ['Güller', 'guller', 'Duygularınızı anlatan klasik ve zarif gül seçenekleri.', 'guller'],
            ['Aranjmanlar', 'aranjmanlar', 'Masa üstü ve yaşam alanları için dengeli çiçek aranjmanları.', 'aranjmanlar'],
            ['Saksı Çiçekleri', 'saksi-cicekleri', 'Uzun süre eşlik eden, bakımı keyifli canlı bitkiler.', 'saksi-cicekleri'],
            ['Özel Günler', 'ozel-gunler', 'Kutlamalara ve anlamlı anlara eşlik eden çiçek tasarımları.', 'ozel-gunler'],
            ['Yeni Bebek Çiçekleri', 'yeni-bebek-cicekleri', 'Yeni başlangıçları kutlamak için yumuşak tonlu tasarımlar.', 'yeni-bebek-cicekleri'],
        ];

        foreach ($categories as $index => [$name, $slug, $description, $image]) {
            Category::query()->updateOrCreate(['slug' => $slug], ['name' => $name, 'description' => $description, 'image_path' => "images/placeholders/{$image}.svg", 'is_active' => true, 'sort_order' => $index + 1]);
        }

        $categoryIds = Category::query()->pluck('id', 'slug');
        $products = [
            ['buketler', 'Bahar Esintisi Buketi', 'bahar-esintisi-buketi', 'BK-101', 'Pudra tonlarında mevsim çiçekleriyle ferah bir buket.', 1250, null, 'buketler', true],
            ['buketler', 'Pembe Lale Buketi', 'pembe-lale-buketi', 'BK-102', 'Zarif pembe lalelerle sade ve neşeli bir seçim.', 980, 850, 'buketler', true],
            ['guller', 'Kırmızı Gül Demeti', 'kirmizi-gul-demeti', 'GL-201', 'Klasik kırmızı güllerle güçlü ve zamansız bir mesaj.', 1450, null, 'guller', true],
            ['guller', 'Pastel Gül Buketi', 'pastel-gul-buketi', 'GL-202', 'Krem ve pudra tonlarının yumuşak uyumu.', 1320, 1190, 'guller', false],
            ['aranjmanlar', 'Mevsim Çiçekleri Aranjmanı', 'mevsim-cicekleri-aranjmani', 'AR-301', 'Renkli mevsim çiçekleriyle canlı masa aranjmanı.', 1670, null, 'aranjmanlar', true],
            ['aranjmanlar', 'Zarif Orkide Aranjmanı', 'zarif-orkide-aranjmani', 'AR-302', 'Orkide ve yeşil dokularla kalıcı bir jest.', 2190, null, 'aranjmanlar', false, Product::STOCK_STATUS_PRE_ORDER],
            ['saksi-cicekleri', 'Beyaz Orkide', 'beyaz-orkide', 'SK-401', 'Aydınlık alanlara uyum sağlayan zarif beyaz orkide.', 1750, null, 'saksi-cicekleri', true, Product::STOCK_STATUS_PRE_ORDER],
            ['saksi-cicekleri', 'Yeşil Köşe Bitkisi', 'yesil-kose-bitkisi', 'SK-402', 'Yaşam alanlarına doğal bir nefes katan saksı bitkisi.', 940, 820, 'saksi-cicekleri', false],
            ['ozel-gunler', 'Yıldönümü Zarafeti', 'yildonumu-zarafeti', 'OG-501', 'Yıldönümleri için romantik tonlarda özel tasarım.', 2350, 2100, 'ozel-gunler', true],
            ['ozel-gunler', 'Doğum Günü Neşesi', 'dogum-gunu-nesesi', 'OG-502', 'Renkli çiçeklerle kutlama enerjisini taşıyan aranjman.', 1580, null, 'ozel-gunler', false],
            ['yeni-bebek-cicekleri', 'Hoş Geldin Bebek Buketi', 'hos-geldin-bebek-buketi', 'YB-601', 'Yeni doğan mutluluğunu kutlayan yumuşak renkli buket.', 1460, null, 'yeni-bebek-cicekleri', true],
            ['yeni-bebek-cicekleri', 'Tatlı Başlangıç Aranjmanı', 'tatli-baslangic-aranjmani', 'YB-602', 'Pastel çiçeklerle hazırlanmış sıcak bir yeni bebek hediyesi.', 1890, 1690, 'yeni-bebek-cicekleri', false],
        ];

        foreach ($products as $index => $data) {
            [$categorySlug, $name, $slug, $sku, $shortDescription, $price, $salePrice, $image, $featured] = $data;
            $stockStatus = $data[9] ?? Product::STOCK_STATUS_IN_STOCK;
            $product = Product::query()->updateOrCreate(
                ['slug' => $slug],
                ['category_id' => $categoryIds[$categorySlug], 'name' => $name, 'sku' => $sku, 'short_description' => $shortDescription, 'description' => $shortDescription.' Başiskele Çiçek ekibi tarafından özenle hazırlanır.', 'price' => $price, 'sale_price' => $salePrice, 'stock_status' => $stockStatus, 'is_active' => true, 'is_featured' => $featured, 'sort_order' => $index + 1],
            );

            $product->images()->updateOrCreate(['image_path' => "images/placeholders/{$image}.svg"], ['alt_text' => $name.' için yer tutucu görsel', 'is_primary' => true, 'sort_order' => 1]);
        }
    }
}
