<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductImage extends Model
{
    /** @use HasFactory<\Database\Factories\ProductImageFactory> */
    use HasFactory;

    protected $fillable = ['product_id', 'image_path', 'alt_text', 'is_primary', 'sort_order'];

    protected $attributes = ['is_primary' => false, 'sort_order' => 0];

    protected function casts(): array
    {
        return ['is_primary' => 'boolean', 'sort_order' => 'integer'];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function imageUrl(): string
    {
        return Str::startsWith($this->image_path, 'products/')
            ? Storage::disk('public')->url($this->image_path)
            : asset($this->image_path);
    }
}
