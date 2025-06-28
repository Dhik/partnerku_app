<?php

namespace App\Domain\Campaign\Requests;

use App\Domain\Campaign\Enums\CampaignContentEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CampaignUpdateContentRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'rate_card' => ['sometimes', 'required', 'numeric', 'min:0'],
            'task_name' => ['sometimes', 'required', 'string', 'max:255'],
            'link' => ['nullable', 'url'],
            'product' => ['sometimes', 'required', 'string', 'max:255'],
            'channel' => ['sometimes', 'required', Rule::in(CampaignContentEnum::PlatformValidation)],
            'boost_code' => ['nullable', 'string'],
            'kode_ads' => ['nullable', 'string', 'max:255'],
            'views' => ['nullable', 'integer', 'min:0'],
            'likes' => ['nullable', 'integer', 'min:0'],
            'comments' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
    
    public function messages(): array
    {
        return [
            'rate_card.required' => 'Rate card wajib diisi.',
            'rate_card.numeric' => 'Rate card harus berupa angka.',
            'rate_card.min' => 'Rate card tidak boleh kurang dari 0.',
            'task_name.required' => 'Task name wajib diisi.',
            'task_name.string' => 'Task name harus berupa teks.',
            'task_name.max' => 'Task name tidak boleh lebih dari 255 karakter.',
            'product.required' => 'Product wajib diisi.',
            'product.string' => 'Product harus berupa teks.',
            'product.max' => 'Product tidak boleh lebih dari 255 karakter.',
            'channel.required' => 'Channel wajib diisi.',
            'channel.in' => 'Channel yang dipilih tidak valid.',
            'link.url' => 'Link harus berupa URL yang valid.',
            'views.integer' => 'Views harus berupa angka.',
            'views.min' => 'Views tidak boleh kurang dari 0.',
            'likes.integer' => 'Likes harus berupa angka.',
            'likes.min' => 'Likes tidak boleh kurang dari 0.',
            'comments.integer' => 'Comments harus berupa angka.',
            'comments.min' => 'Comments tidak boleh kurang dari 0.',
        ];
    }
}