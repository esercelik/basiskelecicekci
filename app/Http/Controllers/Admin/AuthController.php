<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('admin.auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $key = $this->throttleKey($request);

        if (RateLimiter::tooManyAttempts($key, 5)) {
            throw ValidationException::withMessages(['email' => 'Çok fazla deneme yapıldı. Lütfen kısa süre sonra tekrar deneyin.']);
        }

        $credentials = $request->safe()->only(['email', 'password']);

        if (! Auth::attempt(['email' => $credentials['email'], 'password' => $credentials['password'], 'is_admin' => true])) {
            RateLimiter::hit($key, 60);

            throw ValidationException::withMessages(['email' => 'Giriş bilgileri hatalı.']);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        return to_route('admin.dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('admin.login')->with('status', 'Güvenli şekilde çıkış yaptınız.');
    }

    private function throttleKey(Request $request): string
    {
        return Str::transliterate(Str::lower((string) $request->input('email')).'|'.$request->ip());
    }
}
