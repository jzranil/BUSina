<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/** Admin creating or editing an account. */
class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user?->user_id, 'user_id')],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            // Required when creating; optional when editing (blank = keep current password)
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::min(8)],
        ];
    }
}
