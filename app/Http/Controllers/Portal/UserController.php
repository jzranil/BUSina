<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Support\ActivityLogger;
use Illuminate\Http\Request;

/** Admin only: manage accounts and roles. */
class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = '%'.$request->q.'%';
                $query->where(fn ($q) => $q->where('name', 'like', $term)->orWhere('email', 'like', $term));
            })
            ->when($request->filled('role'), fn ($q) => $q->where('role', $request->role))
            ->withCount('reservations')
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return view('portal.users.index', ['users' => $users, 'roles' => User::ROLES]);
    }

    public function create()
    {
        return view('portal.users.create', ['roles' => User::ROLES]);
    }

    public function store(UserRequest $request)
    {
        $data = $request->validated();

        $user = new User(['name' => $data['name'], 'email' => $data['email'], 'password' => $data['password']]);
        $user->role = $data['role']; // role is not mass-assignable on purpose
        $user->save();

        ActivityLogger::log('user.created', "Created {$user->role} account {$user->email}", $user);

        return redirect()->route('portal.users.index')->with('success', "Account for {$user->name} was created.");
    }

    public function edit(User $user)
    {
        return view('portal.users.edit', ['user' => $user, 'roles' => User::ROLES]);
    }

    public function update(UserRequest $request, User $user)
    {
        $data = $request->validated();

        if ($user->is($request->user()) && $data['role'] !== 'admin') {
            return back()->withInput()->with('error', 'You cannot remove your own admin role.');
        }

        $user->fill(['name' => $data['name'], 'email' => $data['email']]);
        if (! empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->role = $data['role'];
        $user->save();

        ActivityLogger::log('user.updated', "Updated account {$user->email} (role: {$user->role})", $user);

        return redirect()->route('portal.users.index')->with('success', "Account for {$user->name} was updated.");
    }

    public function destroy(Request $request, User $user)
    {
        if ($user->is($request->user())) {
            return back()->with('error', 'You cannot delete your own account here.');
        }

        $email = $user->email;
        $user->delete(); // their reservations stay, with user_id set to NULL
        ActivityLogger::log('user.deleted', "Deleted account {$email}");

        return redirect()->route('portal.users.index')->with('success', "Account {$email} was deleted.");
    }
}
