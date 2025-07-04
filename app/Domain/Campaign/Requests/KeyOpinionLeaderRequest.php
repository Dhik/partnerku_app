<?php

namespace App\Domain\Campaign\Requests;

use App\Domain\Campaign\Enums\KeyOpinionLeaderEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KeyOpinionLeaderRequest extends FormRequest
{
    public function rules(): array
    {
        $kolId = $this->route('keyOpinionLeader');
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'channel' => ['required', Rule::in(KeyOpinionLeaderEnum::Channel)],
            'username' => [
                'required', 
                'string',
                'max:255',
                'regex:/^[a-zA-Z0-9_.-]+$/', 
                Rule::unique('key_opinion_leaders')->where(function ($query) {
                    return $query->where('channel', $this->input('channel'));
                })->ignore($kolId)
            ],
            'niche' => ['nullable', 'string', 'max:255'],
            'content_type' => ['nullable', 'string', 'max:255'],
            'rate' => ['nullable', 'numeric', 'integer', 'min:0'],
            'pic_contact' => ['required', 'exists:users,id'],
            'phone_number' => ['nullable', 'string', 'max:20'],
            
            // Video links validation
            'video_10_links' => ['nullable', 'array', 'max:10'],
            'video_10_links.*' => ['nullable', 'url', 'max:500'],
            
            // Additional fields
            'name' => ['nullable', 'string', 'max:255'],
            'link' => ['nullable', 'url', 'max:500'],
            'category' => ['nullable', 'string', 'max:255'],
            'tier' => ['nullable', 'string', 'max:255'],
            'gmv' => ['nullable', 'numeric', 'integer', 'min:0'],
            'pic_listing' => ['nullable', 'string', 'max:255'],
            'pic_content' => ['nullable', 'string', 'max:255'],
            'status_recommendation' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'channel.required' => 'Channel is required.',
            'channel.in' => 'Invalid channel selected.',
            'username.required' => 'Username is required.',
            'username.string' => 'Username must be a text value.',
            'username.max' => 'Username must not exceed 255 characters.',
            'username.regex' => 'Username can only contain letters, numbers, dots, hyphens and underscores.',
            'username.unique' => 'This username already exists for the selected channel.',
            'niche.max' => 'Niche must not exceed 255 characters.',
            'content_type.max' => 'Content type must not exceed 255 characters.',
            'rate.numeric' => 'Rate must be a number.',
            'rate.integer' => 'Rate must be an integer.',
            'rate.min' => 'Rate must be at least 0.',
            'pic_contact.required' => 'PIC Contact is required.',
            'pic_contact.exists' => 'Selected PIC Contact does not exist.',
            'phone_number.max' => 'Phone number must not exceed 20 characters.',
            
            // Video links messages
            'video_10_links.max' => 'You can only add up to 10 video links.',
            'video_10_links.*.url' => 'Each video link must be a valid URL.',
            'video_10_links.*.max' => 'Each video link must not exceed 500 characters.',
            
            // Additional field messages
            'name.max' => 'Name must not exceed 255 characters.',
            'link.url' => 'Link must be a valid URL.',
            'link.max' => 'Link must not exceed 500 characters.',
            'gmv.numeric' => 'GMV must be a number.',
            'gmv.integer' => 'GMV must be an integer.',
            'gmv.min' => 'GMV must be at least 0.',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
    
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation()
    {
        // Clean up numeric fields - convert empty strings to null
        $numericFields = ['rate', 'gmv'];
        
        foreach ($numericFields as $field) {
            if ($this->has($field) && $this->input($field) === '') {
                $this->merge([$field => null]);
            }
        }
        
        // Clean up string fields - trim whitespace
        $stringFields = ['username', 'name', 'phone_number', 'niche', 'content_type', 'pic_listing', 'pic_content'];
        
        foreach ($stringFields as $field) {
            if ($this->has($field) && is_string($this->input($field))) {
                $this->merge([$field => trim($this->input($field))]);
            }
        }
    }
}