<?php

namespace App\Domain\Payroll\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PayrollRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'posisi' => 'required|string|max:255',
            'bulan' => 'required|string|max:255',
            'salary' => 'required|numeric|min:0'
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
            'name.required' => 'Employee name is required',
            'name.max' => 'Employee name must not exceed 255 characters',
            'posisi.required' => 'Position is required',
            'posisi.max' => 'Position must not exceed 255 characters',
            'bulan.required' => 'Month is required',
            'bulan.max' => 'Month must not exceed 255 characters',
            'salary.required' => 'Salary is required',
            'salary.numeric' => 'Salary must be a number',
            'salary.min' => 'Salary must be at least 0'
        ];
    }
}