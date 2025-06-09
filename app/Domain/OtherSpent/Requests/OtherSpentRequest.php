<?php

namespace App\Domain\OtherSpent\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OtherSpentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'date' => 'required|date',
            'detail' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'evidence_link' => 'required|string|max:255'
        ];
    }

    /**
     * Get custom error messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'date.required' => 'Date is required',
            'date.date' => 'Date must be a valid date',
            'detail.required' => 'Detail is required',
            'amount.required' => 'Amount is required',
            'amount.numeric' => 'Amount must be a number',
            'amount.min' => 'Amount must be at least 0',
            'evidence_link.required' => 'Evidence link is required',
            'evidence_link.max' => 'Evidence link must not exceed 255 characters'
        ];
    }
}