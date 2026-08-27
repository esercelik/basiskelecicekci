<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class ProductImageManager
{
    /**
     * @param  array<int, UploadedFile>  $files
     */
    public function addUploads(Product $product, array $files): void
    {
        if ($files === []) {
            return;
        }

        $savedPaths = [];

        try {
            foreach ($files as $file) {
                $savedPaths[] = $file->storePublicly('products/'.$product->id, 'public');
            }

            DB::transaction(function () use ($product, $savedPaths): void {
                $nextOrder = ((int) $product->images()->max('sort_order')) + 1;

                foreach ($savedPaths as $path) {
                    $product->images()->create([
                        'image_path' => $path,
                        'alt_text' => $product->name,
                        'sort_order' => $nextOrder++,
                    ]);
                }

                $this->ensurePrimary($product);
            });
        } catch (Throwable $exception) {
            Storage::disk('public')->delete($savedPaths);

            throw $exception;
        }
    }

    /**
     * @param  array<int|string, string|null>  $altTexts
     * @param  array<int|string, int|string|null>  $sortOrders
     */
    public function updateMetadata(Product $product, array $altTexts, array $sortOrders, ?int $primaryImageId): void
    {
        DB::transaction(function () use ($product, $altTexts, $sortOrders, $primaryImageId): void {
            $images = $product->images()->get();

            foreach ($images as $image) {
                $image->update([
                    'alt_text' => filled($altTexts[$image->id] ?? null) ? trim((string) $altTexts[$image->id]) : null,
                    'sort_order' => max(0, (int) ($sortOrders[$image->id] ?? $image->sort_order)),
                ]);
            }

            $this->ensurePrimary($product, $primaryImageId);
        });
    }

    public function delete(ProductImage $image): void
    {
        $path = $image->image_path;
        $product = $image->product;

        DB::transaction(function () use ($image, $product): void {
            $image->delete();
            $this->ensurePrimary($product);
        });

        $this->deleteManagedPath($path);
    }

    /**
     * @return Collection<int, string>
     */
    public function managedPaths(Product $product): Collection
    {
        return $product->images()
            ->pluck('image_path')
            ->filter(fn (string $path): bool => Str::startsWith($path, 'products/'));
    }

    /**
     * @param  Collection<int, string>  $paths
     */
    public function deleteManagedPaths(Collection $paths): void
    {
        $paths->each(fn (string $path): bool => $this->deleteManagedPath($path));
    }

    private function ensurePrimary(Product $product, ?int $requestedPrimaryId = null): void
    {
        $images = $product->images()->orderBy('sort_order')->orderBy('id')->get();

        if ($images->isEmpty()) {
            return;
        }

        $primary = $requestedPrimaryId ? $images->firstWhere('id', $requestedPrimaryId) : $images->firstWhere('is_primary', true);
        $primary ??= $images->first();

        $product->images()->update(['is_primary' => false]);
        $primary->update(['is_primary' => true]);
    }

    private function deleteManagedPath(string $path): bool
    {
        if (! Str::startsWith($path, 'products/')) {
            return true;
        }

        if (! Storage::disk('public')->delete($path)) {
            throw new RuntimeException('Ürün görseli depolama alanından silinemedi.');
        }

        return true;
    }
}
