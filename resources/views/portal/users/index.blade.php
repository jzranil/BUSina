<x-app-layout title="Users">
    <x-page-header title="Users" subtitle="Admin, employee and customer accounts.">
        <a href="{{ route('portal.users.create') }}" class="btn-primary"><x-icon name="plus" class="h-4 w-4" /> Add user</a>
    </x-page-header>

    @php $filtered = request()->hasAny(['q', 'role']); @endphp

    <div class="card">
        <form method="GET" class="flex flex-col gap-3 border-b border-slate-100 p-4 sm:flex-row sm:items-center">
            <div class="relative flex-1">
                <x-icon name="search" class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" />
                <input type="search" name="q" value="{{ request('q') }}" placeholder="Search name or email" class="field pl-9" aria-label="Search users">
            </div>
            <select name="role" class="field sm:w-44" aria-label="Filter by role">
                <option value="">All roles</option>
                @foreach ($roles as $value => $label)
                    <option value="{{ $value }}" @selected(request('role') === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button class="btn-secondary"><x-icon name="filter" class="h-4 w-4" /> Filter</button>
                @if ($filtered)
                    <a href="{{ route('portal.users.index') }}" class="btn-ghost">Clear</a>
                @endif
            </div>
        </form>

        @if ($users->isEmpty())
            <x-empty-state icon="users" title="No users match" />
        @else
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead><tr><th>Name</th><th>Email</th><th>Role</th><th>Reservations</th><th>Joined</th><th class="text-right">Actions</th></tr></thead>
                    <tbody>
                        @foreach ($users as $u)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-xs font-bold text-slate-600">{{ strtoupper(mb_substr($u->name, 0, 1)) }}</span>
                                        <span class="font-medium text-slate-900">{{ $u->name }}</span>
                                        @if ($u->is(auth()->user())) <span class="text-xs text-slate-400">(you)</span> @endif
                                    </div>
                                </td>
                                <td>{{ $u->email }}</td>
                                <td><x-badge :status="$u->role">{{ $roles[$u->role] ?? $u->role }}</x-badge></td>
                                <td>{{ $u->reservations_count }}</td>
                                <td>{{ $u->created_at->format('M j, Y') }}</td>
                                <td>
                                    <div class="flex justify-end gap-1">
                                        <a href="{{ route('portal.users.edit', $u) }}" class="btn-ghost btn-sm" title="Edit"><x-icon name="pencil" class="h-4 w-4" /><span class="sr-only">Edit {{ $u->name }}</span></a>
                                        @unless ($u->is(auth()->user()))
                                            <x-confirm-action :action="route('portal.users.destroy', $u)" method="DELETE"
                                                              title="Delete {{ $u->name }}?" message="Their reservations are kept but will no longer be linked to an account." confirm="Delete"
                                                              button-class="btn-ghost btn-sm text-red-600 hover:bg-red-50 hover:text-red-700" title="Delete">
                                                <x-icon name="trash" class="h-4 w-4" /><span class="sr-only">Delete {{ $u->name }}</span>
                                            </x-confirm-action>
                                        @endunless
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($users->hasPages())
                <div class="border-t border-slate-100 px-4 py-3">{{ $users->links() }}</div>
            @endif
        @endif
    </div>
</x-app-layout>
