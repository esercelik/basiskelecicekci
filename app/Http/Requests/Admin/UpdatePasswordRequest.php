<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\Attributes\ErrorBag;
use Illuminate\Foundation\Http\FormRequest;

#[ErrorBag('password')]
class UpdatePasswordRequest extends FormRequest
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
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
        ];
    }
}
