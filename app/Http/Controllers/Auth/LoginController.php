<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View|RedirectResponse
    {
        return Auth::check()
            ? redirect()->to($this->homeFor(Auth::user()))
            : view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        return redirect()->intended($this->homeFor($request->user()));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /** Each role lands in its own panel. */
    private function homeFor(\App\Models\User $user): string
    {
        return $user->isSuperAdmin()
            ? route('admin.dashboard')
            : route('consultant.dashboard');
    }
}
