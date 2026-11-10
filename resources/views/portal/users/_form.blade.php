{{-- Shared add/edit form. $user is null when adding. --}}
@csrf
<div class="grid gap-5 sm:grid-cols-2">
    <x-form.input name="name" label="Full name" :value="$user?->name" required autocomplete="off" />
    <x-form.input name="email" type="email" label="Email address" :value="$user?->email" required autocomplete="off" />
    <x-form.input name="contact_number" type="tel" label="Contact number" :value="$user?->contact_number" placeholder="09171234567" />
    <x-form.select name="role" label="Role" :options="$roles" :value="$user?->role ?? 'employee'" required />
    <x-form.password name="password" :label="$user ? 'New password' : 'Password'" :required="! $user" autocomplete="new-password"
                     :hint="$user ? 'Leave blank to keep the current password.' : 'At least 8 characters.'" />
    <x-form.password name="password_confirmation" label="Confirm password" :required="! $user" autocomplete="new-password" />
</div>
