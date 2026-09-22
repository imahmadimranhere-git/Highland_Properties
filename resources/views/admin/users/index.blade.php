@extends('layouts.admin')

@section('title', 'Users & Roles')

@section('content')
    <x-panel.page-head title="Users & Roles" sub="Everyone who can sign in to the admin panel or the consultant portal.">
        <x-slot:actions>
            <a href="{{ route('admin.users.create') }}" class="btn btn--primary btn--sm">
                <x-ui.icon name="plus" :size="16" /> Add user
            </a>
        </x-slot:actions>
    </x-panel.page-head>

    <x-panel.box flush>
        <x-slot:actions>
            <form method="GET" class="u-flex u-gap-8">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Name or email" style="width:200px;">
                <select name="role" class="form-select" style="width:170px;">
                    <option value="">Any role</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" @selected(request('role') == $role->id)>{{ $role->name }}</option>
                    @endforeach
                </select>
                <button class="btn btn--secondary btn--sm">Filter</button>
            </form>
        </x-slot:actions>

        <div class="table-wrap">
            <table class="table-hp">
                <thead>
                    <tr><th>Name</th><th>Role</th><th>Phone</th><th>Open leads</th><th>Status</th><th class="is-actions">Actions</th></tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>
                                <strong>{{ $user->name }}</strong>
                                <span style="display:block;font-size:.8125rem;" class="text-muted-hp">{{ $user->email }}</span>
                            </td>
                            <td>{{ $user->role?->name }}</td>
                            <td>{{ $user->phone ?: '—' }}</td>
                            <td>{{ $user->open_leads }}</td>
                            <td>
                                <span class="badge {{ $user->is_active ? 'badge-success' : 'badge-muted' }}">
                                    {{ $user->is_active ? 'Active' : 'Deactivated' }}
                                </span>
                            </td>
                            <td class="is-actions">
                                <a href="{{ route('admin.users.edit', $user) }}" class="btn-icon" aria-label="Edit">
                                    <x-ui.icon name="pencil" :size="16" />
                                </a>

                                @unless ($user->is(auth()->user()))
                                    <form method="POST" action="{{ route('admin.users.toggle', $user) }}" style="display:inline;"
                                          data-confirm-label="{{ $user->is_active ? 'Deactivate' : 'Activate' }}"
                                          data-confirm="{{ $user->is_active ? 'Deactivate ' . $user->name . '? They will be signed out immediately.' : 'Reactivate ' . $user->name . '?' }}">
                                        @csrf @method('PATCH')
                                        <button class="btn-icon" aria-label="{{ $user->is_active ? 'Deactivate' : 'Activate' }}"
                                                title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                                            <x-ui.icon name="shield" :size="16" />
                                        </button>
                                    </form>

                                    <x-ui.delete-form :action="route('admin.users.destroy', $user)" :confirm="'Delete ' . $user->name . '\'s account?'" />
                                @endunless
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $users->links() }}
    </x-panel.box>
@endsection
