<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\ProductFactory> */
    use HasFactory;

    public const STOCK_STATUS_IN_STOCK = 'in_stock';
    public const STOCK_STATUS_PRE_ORDER = 'pre_order';
    public const STOCK_STATUS_OUT_OF_STOCK = 'out_of_stock';

    protected $fillable = ['category_id', 'name', 'slug', 'sku', 'short_description', 'description', 'price', 'sale_price', 'stock_status', 'is_active', 'is_featured', 'sort_order'];

    protected $attributes = ['stock_status' => self::STOCK_STATUS_IN_STOCK, 'is_active' => true, 'is_featured' => false, 'sort_order' => 0];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'sale_price' => 'decimal:2', 'is_active' => 'boolean', 'is_featured' => 'boolean', 'sort_order' => 'integer'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBindingQuery(mixed $query, mixed $value, ?string $field = null): mixed
    {
        return $query->active()->whereHas('category', fn (Builder $categoryQuery): Builder => $categoryQuery->active())->where($field ?? $this->getRouteKeyName(), $value);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(ProductImage::class)->where('is_primary', true)->orderBy('sort_order');
    }

    public function scopeActive(Builder $query): void
    {
        $query->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): void
    {
        $query->where('is_featured', true);
    }

    public function scopeOrdered(Builder $query): void
    {
        $query->orderBy('sort_order')->orderByDesc('id');
    }

    public function hasSalePrice(): bool
    {
        return ! is_null($this->sale_price) && (float) $this->sale_price < (float) $this->price;
    }

    public function currentPrice(): float
    {
        return $this->hasSalePrice() ? (float) $this->sale_price : (float) $this->price;
    }

    public function formattedPrice(): string
    {
        return (string) Number::currency((float) $this->price, 'TRY', 'tr_TR');
    }

    public function formattedCurrentPrice(): string
    {
        return (string) Number::currency($this->currentPrice(), 'TRY', 'tr_TR');
    }

    public function stockLabel(): string
    {
        return match ($this->stock_status) {
            self::STOCK_STATUS_IN_STOCK => 'Stokta',
            self::STOCK_STATUS_PRE_ORDER => 'Ön siparişle hazırlanır',
            default => 'Stokta yok',
        };
    }

    public function isOrderable(): bool
    {
        return $this->stock_status !== self::STOCK_STATUS_OUT_OF_STOCK;
    }

    public function whatsappOrderUrl(): ?string
    {
        $productUrl = route('products.show', $this);
        $message = sprintf('Merhaba, %s web sitenizdeki %s ürünü hakkında sipariş vermek istiyorum. Ürün bağlantısı: %s', config('store.name'), $this->name, $productUrl);

        return self::whatsappUrlForMessage($message);
    }

    public static function generalWhatsappUrl(): ?string
    {
        return self::whatsappUrlForMessage(sprintf('Merhaba, %s ile çiçek siparişi hakkında bilgi almak istiyorum.', config('store.name')));
    }

    private static function whatsappUrlForMessage(string $message): ?string
    {
        $number = self::normalizedWhatsappNumber();

        return is_null($number) ? null : 'https://wa.me/'.$number.'?'.http_build_query(['text' => $message], '', '&', PHP_QUERY_RFC3986);
    }

    private static function normalizedWhatsappNumber(): ?string
    {
        $number = preg_replace('/\D+/', '', (string) config('store.whatsapp_number'));

        if (is_null($number) || $number === '') {
            return null;
        }

        $number = Str::startsWith($number, '00') ? Str::substr($number, 2) : $number;

        if (Str::startsWith($number, '0')) {
            $number = '90'.Str::substr($number, 1);
        }

        return preg_match('/^[1-9][0-9]{7,14}$/', $number) === 1 ? $number : null;
    }
}
