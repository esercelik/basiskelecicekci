<?php

namespace App\Http\Requests\Admin;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;

class StoreCategoryRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:140', 'alpha_dash:ascii', 'unique:categories,slug'],
            'description' => ['nullable', 'string', 'max:2000'],
            'image' => ['nullable', File::image()->types(['jpg', 'jpeg', 'png', 'webp'])->extensions(['jpg', 'jpeg', 'png', 'webp'])->max('5mb')],
            'is_active' => ['required', 'boolean'],
            'sort_order' => ['required', 'integer', 'min:0', 'max:999999'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'slug' => $this->filled('slug') ? Str::slug((string) $this->input('slug')) : $this->uniqueSlug((string) $this->input('name')),
            'description' => $this->filled('description') ? strip_tags((string) $this->input('description')) : null,
        ]);
    }

    private function uniqueSlug(string $name): string
    {
        $slug = Str::slug($name) ?: 'kategori';
        $base = $slug;
        $suffix = 2;

        while (Category::query()->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }
}
