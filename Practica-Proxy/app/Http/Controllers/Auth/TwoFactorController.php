<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\TwoFactorCodeMail;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Rules\Recaptcha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class TwoFactorController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('two_factor_user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string', 'size:6'],
            'recaptcha_token' => [new Recaptcha('two_factor')],
        ]);

        $userId = $request->session()->get('two_factor_user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        if (! $user) {
            $request->session()->forget('two_factor_user_id');
            return redirect()->route('login');
        }

        if (! $user->two_factor_code || $user->two_factor_expires_at->isPast()) {
            return back()->withErrors(['code' => 'El código ha expirado. Solicita uno nuevo.']);
        }

        if ($request->code !== $user->two_factor_code) {
            return back()->withErrors(['code' => 'El código es incorrecto.']);
        }

        $user->clearTwoFactorCode();

        $request->session()->forget('two_factor_user_id');

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    public function resend(Request $request): RedirectResponse
    {
        $userId = $request->session()->get('two_factor_user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $key = 'two-factor-resend:' . $userId;

        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors(['code' => "Espera {$seconds} segundos antes de reenviar."]);
        }

        RateLimiter::hit($key, 60);

        $user = User::find($userId);

        if (! $user) {
            $request->session()->forget('two_factor_user_id');
            return redirect()->route('login');
        }

        $user->generateTwoFactorCode();
        Mail::to($user)->send(new TwoFactorCodeMail($user));

        return back()->with('status', 'Se ha enviado un nuevo código a tu correo.');
    }
}
