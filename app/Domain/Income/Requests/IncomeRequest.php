<?php

namespace App\Domain\Income\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;
use App\Domain\User\Models\User;

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
            'team_in_charge' => 'required'  // We'll validate this in the custom validation method
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $this->validateTeamInCharge($validator);
        });
    }

    /**
     * Custom validation for team_in_charge field
     */
    protected function validateTeamInCharge($validator)
    {
        $teamInCharge = $this->input('team_in_charge');

        // Check if team_in_charge is provided
        if (empty($teamInCharge)) {
            $validator->errors()->add('team_in_charge', 'At least one team member must be selected.');
            return;
        }

        // Normalize the data
        $userIds = $this->normalizeTeamInCharge($teamInCharge);

        if (empty($userIds)) {
            $validator->errors()->add('team_in_charge', 'At least one valid team member must be selected.');
            return;
        }

        // Check if all user IDs are valid integers
        foreach ($userIds as $userId) {
            if (!is_numeric($userId) || intval($userId) != $userId || intval($userId) <= 0) {
                $validator->errors()->add('team_in_charge', 'All team member IDs must be valid positive integers.');
                return;
            }
        }

        // Check if all users exist and are verified
        $validUserIds = User::whereIn('id', $userIds)
            ->pluck('id')
            ->toArray();

        if (count($validUserIds) !== count($userIds)) {
            $invalidIds = array_diff($userIds, $validUserIds);
            Log::warning('Invalid user IDs in team_in_charge', [
                'invalid_ids' => $invalidIds,
                'submitted_ids' => $userIds
            ]);
            $validator->errors()->add('team_in_charge', 'Some selected team members are invalid or not verified.');
            return;
        }
    }

    /**
     * Normalize team_in_charge to array of integers
     */
    protected function normalizeTeamInCharge($teamInCharge)
    {
        if (empty($teamInCharge)) {
            return [];
        }

        // If it's a string, try to decode as JSON first
        if (is_string($teamInCharge)) {
            $decoded = json_decode($teamInCharge, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $teamInCharge = $decoded;
            } else {
                // If not valid JSON, treat as comma-separated string or single value
                $teamInCharge = explode(',', $teamInCharge);
            }
        }

        // Ensure it's an array
        if (!is_array($teamInCharge)) {
            $teamInCharge = [$teamInCharge];
        }

        // Filter out empty values and convert to integers
        $teamInCharge = array_filter(array_map('intval', array_filter($teamInCharge)));

        // Remove duplicates and reindex
        return array_values(array_unique($teamInCharge));
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
            'nama_client.string' => 'Client name must be a string',
            'nama_client.max' => 'Client name must not exceed 255 characters',
            
            'revenue_contract.required' => 'Revenue contract is required',
            'revenue_contract.numeric' => 'Revenue contract must be a number',
            'revenue_contract.min' => 'Revenue contract must be at least 0',
            
            'service.required' => 'Service is required',
            'service.string' => 'Service must be a string',
            'service.max' => 'Service must not exceed 255 characters',
            
            'team_in_charge.required' => 'Team in charge is required',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes()
    {
        return [
            'nama_client' => 'client name',
            'revenue_contract' => 'revenue contract',
            'service' => 'service',
            'team_in_charge' => 'team members'
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        Log::warning('Income validation failed', [
            'errors' => $validator->errors()->toArray(),
            'user_id' => auth()->id()
        ]);

        parent::failedValidation($validator);
    }
}