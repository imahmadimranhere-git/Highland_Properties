@extends('layouts.consultant')

@section('title', 'My Profile')

@section('content')
    <x-panel.page-head title="My Profile" :sub="$user->email" />

    <div class="row">
        <div class="col-lg-7">
            <x-panel.box title="Details">
                <form method="POST" action="{{ route('consultant.profile.update') }}" enctype="multipart/form-data" novalidate>
                    @csrf @method('PUT')

                    <div class="u-flex u-gap-16 u-mb-24" style="align-items:center;">
                        @if ($user->avatar)
                            <img src="{{ Storage::disk('public')->url($user->avatar) }}" alt="" width="72" height="72"
                                 style="border-radius:50%;object-fit:cover;">
                        @else
                            <span class="panel-user__avatar" style="width:72px;height:72px;font-size:1.25rem;">
                                {{ \Illuminate\Support\Str::of($user->name)->substr(0, 2)->upper() }}
                            </span>
                        @endif
                        <div class="form-group u-mb-0" style="flex:1;">
                            <label class="form-label" for="avatar">Photo</label>
                            <input id="avatar" name="avatar" type="file" accept="image/*" class="form-control @error('avatar') is-invalid @enderror">
                            @error('avatar')<span class="form-error">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="name">Full name</label>
                        <input id="name" name="name" type="text" maxlength="150" required
                               class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}">
                        @error('name')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label class="form-label" for="phone">Phone</label>
                            <input id="phone" name="phone" type="tel" maxlength="30" class="form-control" value="{{ old('phone', $user->phone) }}">
                        </div>
                        <div class="col-md-6 form-group">
                            <label class="form-label" for="designation">Designation</label>
                            <input id="designation" name="designation" type="text" maxlength="120" class="form-control"
                                   value="{{ old('designation', $user->designation) }}">
                        </div>
                    </div>

                    <p class="form-hint">To change your sign-in email, ask the administrator.</p>
                    <button class="btn btn--primary">Save profile</button>
                </form>
            </x-panel.box>
        </div>

        <div class="col-lg-5">
            <x-panel.box title="Change password">
                <form method="POST" action="{{ route('consultant.profile.password') }}" novalidate>
                    @csrf @method('PUT')

                    <div class="form-group">
                        <label class="form-label" for="current_password">Current password</label>
                        <input id="current_password" name="current_password" type="password" autocomplete="current-password"
                               class="form-control @error('current_password') is-invalid @enderror">
                        @error('current_password')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password">New password</label>
                        <input id="password" name="password" type="password" autocomplete="new-password"
                               class="form-control @error('password') is-invalid @enderror">
                        <span class="form-hint">At least 8 characters with letters and numbers.</span>
                        @error('password')<span class="form-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="password_confirmation">Confirm new password</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" autocomplete="new-password" class="form-control">
                    </div>

                    <button class="btn btn--primary">Change password</button>
                </form>
            </x-panel.box>
        </div>
    </div>
@endsection
