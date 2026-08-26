# Başiskele Çiçek

Başiskele/Kocaeli’deki yerel çiçekçi için ürün kataloğu ve WhatsApp sipariş akışı sunan Laravel uygulaması.

## Kurulum

Gereksinimler: PHP 8.3+, Composer ve Node.js 20+.

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link
npm install
npm run build
php artisan serve
```

Uygulama varsayılan olarak `http://127.0.0.1:8000` adresinde açılır. Geliştirme sırasında Vite izleme sunucusunu çalıştırmak için ayrı bir terminalde `npm run dev` kullanabilirsiniz.

## Yönetim paneli

İlk yönetici hesabını güvenli biçimde oluşturmak için:

```bash
php artisan admin:create
```

Giriş adresi: `/admin/giris`.

Ürün ve kategori görselleri `storage/app/public/products` ile `storage/app/public/categories` altında tutulur; bunların ziyaretçi sitesinde görünmesi için `php artisan storage:link` gereklidir. Mağaza adı, iletişim ve WhatsApp bilgileri yönetim panelindeki **Mağaza ayarları** bölümünden değiştirilir.

## Config fallback mağaza bilgileri

Gerçek telefon, WhatsApp, Instagram ve harita bağlantılarını `.env` içindeki aşağıdaki değerlerden değiştirin:

- `STORE_NAME`
- `STORE_PHONE`
- `STORE_WHATSAPP_NUMBER`
- `STORE_ADDRESS`
- `STORE_INSTAGRAM_URL`
- `STORE_MAP_URL`

Bu değerler `config/store.php` üzerinden uygulamanın tamamına merkezi olarak aktarılır. Yeni ayarları production ortamında etkinleştirmek için `php artisan config:clear` çalıştırın.

## Kontroller

```bash
php artisan migrate --seed
php artisan test
vendor/bin/pint --format agent
npm run build
php artisan route:list
```
