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
            'contact_number' => ['nullable', 'regex:/^(09|\+639)\d{9}$/'],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            // Required when creating; optional when editing (blank = keep current password)
            'password' => [$user ? 'nullable' : 'required', 'confirmed', Password::defaults()],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['contact_number' => preg_replace('/[\s-]/', '', (string) $this->contact_number) ?: null]);
    }

    public function messages(): array
    {
        return [
            'contact_number.regex' => 'Enter a Philippine mobile number, like 09171234567 or +639171234567.',
        ];
    }
}
