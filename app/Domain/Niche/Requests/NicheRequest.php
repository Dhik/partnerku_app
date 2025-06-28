<?php

namespace App\Domain\Niche\Requests;

use Illuminate\Foundation\Http\FormRequest;

class NicheRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'name' => 'nullable|string|max:255'
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.string' => 'Niche name must be a string.',
            'name.max' => 'Niche name may not be greater than 255 characters.'
        ];
    }
}