<?php

namespace App\Http\Requests\Admin;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:160'],
            'slug' => ['required', 'string', 'max:180', 'alpha_dash:ascii', Rule::unique('products', 'slug')->ignore($this->product)],
            'sku' => ['nullable', 'string', 'max:100', Rule::unique('products', 'sku')->ignore($this->product)],
            'short_description' => ['required', 'string', 'max:500'],
            'description' => ['nullable', 'string', 'max:10000'],
            'price' => ['required', 'numeric', 'gt:0'],
            'sale_price' => ['nullable', 'numeric', 'gt:0', 'lt:price'],
            'stock_status' => ['required', 'in:in_stock,pre_order,out_of_stock'],
            'is_active' => ['required', 'boolean'],
            'is_featured' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999999'],
            'images' => ['nullable', 'array', 'max:10'],
            'images.*' => [File::image()->types(['jpg', 'jpeg', 'png', 'webp'])->extensions(['jpg', 'jpeg', 'png', 'webp'])->max('5mb')],
            'image_alts' => ['nullable', 'array'],
            'image_alts.*' => ['nullable', 'string', 'max:255'],
            'image_order' => ['nullable', 'array'],
            'image_order.*' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'primary_image_id' => ['nullable', 'integer'],
        ];
    }

    public function after(): array
    {
        return [function ($validator): void {
            $product = $this->route('product');
            $existingImages = $product?->images()->count() ?? 0;

            if ($existingImages + count($this->file('images', [])) > 10) {
                $validator->errors()->add('images', 'Bir üründe en fazla 10 görsel bulunabilir.');
            }
        }];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => $this->filled('slug') ? Str::slug((string) $this->input('slug')) : $this->uniqueSlug((string) $this->input('name')),
            'short_description' => strip_tags((string) $this->input('short_description')),
            'description' => $this->filled('description') ? strip_tags((string) $this->input('description')) : null,
            'sku' => $this->filled('sku') ? trim((string) $this->input('sku')) : null,
        ]);
    }

    private function uniqueSlug(string $name): string
    {
        $product = $this->route('product');
        $slug = Str::slug($name) ?: 'urun';
        $base = $slug;
        $suffix = 2;

        while (Product::query()->where('slug', $slug)->whereKeyNot($product?->id)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
