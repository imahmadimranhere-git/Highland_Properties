@extends('layouts.auth')

@section('title', 'Sign in')

@section('content')
    {{-- Rounded floating bar: just the logo and a way back to the site. --}}
    <header class="auth-bar">
        <a href="{{ route('home') }}" class="auth-bar__brand">
            @if (setting('logo'))
                <img src="{{ Storage::disk('public')->url(setting('logo')) }}"
                     alt="{{ setting('site_name', 'Highland Properties') }}" height="34">
            @else
                <span>{{ \Illuminate\Support\Str::before(setting('site_name', 'Highland Properties'), ' ') }}</span>
                <em>{{ \Illuminate\Support\Str::after(setting('site_name', 'Highland Properties'), ' ') }}</em>
            @endif
        </a>

        <a href="{{ route('home') }}" class="auth-bar__link">
            <svg viewBox="0 0 24 24" width="15" height="15" fill="none" stroke="currentColor"
                 stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M19 12H5M11 6l-6 6 6 6"/>
            </svg>
            Back to website
        </a>
    </header>

    <main class="auth-wrap">
        <div class="auth-card">
            <div class="auth-card__head">
                <p class="section-label">Staff access</p>
                <h1 class="auth-card__title">Sign in</h1>
                <hr class="gold-divider">
                <p class="auth-card__note">For administrators and sales consultants only.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" novalidate>
                @csrf

                @if ($errors->any())
                    <div class="alert alert--danger">{{ $errors->first() }}</div>
                @endif

                <div class="form-group">
                    <label class="form-label" for="email">Email address</label>
                    <input id="email" name="email" type="email" inputmode="email" autocomplete="username"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email') }}" required autofocus>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <input id="password" name="password" type="password" autocomplete="current-password"
                           class="form-control @error('password') is-invalid @enderror" required>
                </div>

                <div class="form-check">
                    <input id="remember" name="remember" type="checkbox" value="1">
                    <label for="remember">Keep me signed in</label>
                </div>

                <button type="submit" class="btn btn--primary btn--block u-mt-16">Sign in</button>
            </form>

            <p class="auth-card__foot">
                Accounts are created by the administrator. Forgot your password? Ask them to reset it.
            </p>
        </div>
    </main>
@endsection
