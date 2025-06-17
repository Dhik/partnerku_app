<?php

namespace App\Domain\User\Requests;

use App\Domain\User\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        // Get the current authenticated user
        $currentUser = auth()->user();

        // Only SuperAdmin can create users
        if ($currentUser->hasRole(RoleEnum::SuperAdmin)) {
            // SuperAdmin can create any user except other SuperAdmins
            $forbiddenRoles = [RoleEnum::SuperAdmin];

            // Check if trying to assign SuperAdmin role
            if (count(array_intersect($this->input('roles', []), $forbiddenRoles)) > 0) {
                return false; // SuperAdmin cannot create other SuperAdmins
            }

            // SuperAdmin can assign any tenants, so return true
            return true;
        }

        // All other roles cannot create users
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['string', 'required', 'max:255'],
            'email' => ['email', 'required', 'unique:users,email'],
            'phone_number' => ['string', 'required', 'max:255', 'unique:users,phone_number'],
            'position' => ['string', 'required', 'max:255'],
            'roles' => ['required', 'array', 'min:1'],
            'roles.*' => ['string', 'exists:roles,name'],
            'tenants' => ['nullable', 'array'],
            'tenants.*' => ['integer', 'exists:tenants,id'],
            'password' => ['required', 'confirmed', 'min:6'],
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
            'roles.required' => 'Please select at least one role.',
            'roles.min' => 'Please select at least one role.',
            'roles.*.exists' => 'One or more selected roles are invalid.',
            'tenants.*.exists' => 'One or more selected tenants are invalid.',
            'position.required' => 'Position field is required.',
        ];
    }
}