<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        // Accept "0917 123 4567" or "0917-123-4567"
        $input['contact_number'] = preg_replace('/[\s-]/', '', (string) ($input['contact_number'] ?? ''));

        Validator::make($input, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'contact_number' => ['required', 'regex:/^(09|\+639)\d{9}$/'],
            'password' => $this->passwordRules(),
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ], [
            'contact_number.regex' => 'Enter a Philippine mobile number, like 09171234567 or +639171234567.',
        ])->validate();

        // New sign-ups always get the database default role: customer
        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'contact_number' => $input['contact_number'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
