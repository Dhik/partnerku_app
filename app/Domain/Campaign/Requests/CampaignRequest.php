<?php

namespace App\Domain\Campaign\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CampaignRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'period' => 'required|string',
            'description' => 'required|string',
            'cpm_benchmark' => 'nullable|numeric|min:0', // Add this validation rule
            'id_budget' => 'nullable|integer|exists:budgets,id', // Adjust table name as needed
        ];
    }

    /**
     * Get custom error messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'Campaign title is required.',
            'title.string' => 'Campaign title must be a string.',
            'title.max' => 'Campaign title may not be greater than 255 characters.',
            'period.required' => 'Campaign period is required.',
            'description.required' => 'Campaign description is required.',
            'cpm_benchmark.numeric' => 'CPM benchmark must be a number.',
            'cpm_benchmark.min' => 'CPM benchmark must be at least 0.',
        ];
    }
}