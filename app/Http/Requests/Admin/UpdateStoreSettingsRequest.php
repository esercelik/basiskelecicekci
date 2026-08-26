<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStoreSettingsRequest extends FormRequest
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
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:50'],
            'whatsapp_number' => ['required', 'string', 'max:50'],
            'address' => ['required', 'string', 'max:2000'],
            'instagram_url' => ['nullable', 'url:http,https', 'max:255'],
            'map_url' => ['nullable', 'url:http,https', 'max:255'],
        ];
    }
}
