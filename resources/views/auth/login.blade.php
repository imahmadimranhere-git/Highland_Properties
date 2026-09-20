@extends('layouts.auth')

@section('title', 'Sign in')

@section('content')
    <main class="auth-wrap">
        <div class="auth-card">
            <div class="auth-card__head">
                <p class="section-label">Highland Properties</p>
                <h1 class="auth-card__title">Sign in</h1>
                <hr class="gold-divider">
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
                Forgot your password? Ask the administrator to reset it for you.
            </p>
        </div>

        <p class="auth-back"><a href="{{ route('home') }}">Back to the website</a></p>
    </main>
@endsection
