@extends('layouts.admin')

@section('title', 'Edit user')

@section('content')
    <x-panel.page-head :title="$user->name" :sub="$user->email" />

    <x-panel.box title="Account details">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" novalidate>
            @method('PUT')
            @include('admin.users._form', ['submit' => 'Save changes'])
        </form>
    </x-panel.box>

    <x-panel.box title="Reset password">
        <form method="POST" action="{{ route('admin.users.password', $user) }}" class="row" novalidate
              data-confirm-label="Reset password"
              data-confirm="Set a new password for {{ $user->name }}? Their current password stops working.">
            @csrf @method('PATCH')

            <div class="col-md-5 form-group">
                <label class="form-label" for="new_password">New password</label>
                <input id="new_password" name="password" type="password" autocomplete="new-password"
                       class="form-control @error('password') is-invalid @enderror">
                @error('password')<span class="form-error">{{ $message }}</span>@enderror
            </div>

            <div class="col-md-5 form-group">
                <label class="form-label" for="new_password_confirmation">Confirm</label>
                <input id="new_password_confirmation" name="password_confirmation" type="password"
                       autocomplete="new-password" class="form-control">
            </div>

            <div class="col-md-2 form-group" style="display:flex;align-items:flex-end;">
                <button class="btn btn--primary btn--block">Reset</button>
            </div>
        </form>
    </x-panel.box>
@endsection
