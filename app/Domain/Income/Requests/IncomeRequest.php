<?php

namespace App\Domain\Income\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IncomeRequest extends FormRequest
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
            'nama_client' => 'required|string|max:255',
            'revenue_contract' => 'required|numeric|min:0',
            'service' => 'required|string|max:255',
            'team_in_charge' => 'required|string'
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
            'nama_client.required' => 'Client name is required',
            'nama_client.max' => 'Client name must not exceed 255 characters',
            'revenue_contract.required' => 'Revenue contract is required',
            'revenue_contract.numeric' => 'Revenue contract must be a number',
            'revenue_contract.min' => 'Revenue contract must be at least 0',
            'service.required' => 'Service is required',
            'service.max' => 'Service must not exceed 255 characters',
            'team_in_charge.required' => 'Team in charge is required'
        ];
    }
}