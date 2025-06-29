<?php

namespace App\Domain\User\Requests;

use App\Domain\User\Enums\RoleEnum;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
{
    $currentUser = auth()->user();

    if ($currentUser->hasRole(RoleEnum::SuperAdmin)) {
        // Superadmin can assign any role and any tenant
        return true;
    }

    // For non-superadmin users, apply the tenant restrictions
    $assignedTenants = $this->input('tenants', []);
    $currentUserTenants = $currentUser->tenants()->get()->pluck('id')->map('strval')->toArray();

    if (empty($assignedTenants)) {
        return true;
    }

    $missingTenants = array_diff($assignedTenants, $currentUserTenants);

    if (!empty($missingTenants)) {
        return false;
    }

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
            'name' => ['string', 'required', 'max:255'],
            'email' => ['email', 'required', 'unique:users,email,'.$this->user->id],
            'phone_number' => ['string', 'required', 'max:255', 'unique:users,phone_number,'.$this->user->id],
            'position' => ['string', 'max:255'],
            'roles' => ['required'],
        ];
    }
}
