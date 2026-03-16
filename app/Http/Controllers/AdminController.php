<?php

namespace App\Http\Controllers;

use App\Support\MathCaptcha;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function refreshCaptcha(Request $request): JsonResponse
    {
        MathCaptcha::regenerate($request, 'admin');

        return response()
            ->json(['question' => MathCaptcha::question($request, 'admin')])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    /**
     * Show the admin login form.
     */
    public function showLoginForm(Request $request)
    {
        if (Auth::check() && Auth::user()->canAccessAdminPanel()) {
            return redirect()->route('admin.dashboard');
        }

        MathCaptcha::ensure($request, 'admin');
        $captchaQuestion = MathCaptcha::question($request, 'admin');

        return view('admin.auth.login', compact('captchaQuestion'));
    }

    /**
     * Handle admin login request.
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'captcha_answer' => ['required', 'integer'],
        ]);

        if (! MathCaptcha::isValid($request, 'admin')) {
            MathCaptcha::regenerate($request, 'admin');

            return back()->withErrors([
                'captcha_answer' => 'Incorrect math captcha answer. Please try again.',
            ])->withInput($request->except('password', 'captcha_answer'));
        }

        $credentials = [
            'email' => $validated['email'],
            'password' => $validated['password'],
        ];

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->canAccessAdminPanel()) {
                $request->session()->regenerate();
                MathCaptcha::regenerate($request, 'admin');

                return redirect()->intended(route('admin.dashboard'));
            }

            Auth::logout();

            return back()->withErrors([
                'email' => 'This account does not have admin privileges.',
            ]);
        }

        MathCaptcha::regenerate($request, 'admin');

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->except('password', 'captcha_answer'));
    }

    /**
     * Handle admin logout request.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * Show admin dashboard.
     */
    public function dashboard()
    {
        return view('admin.dashboard');
    }
}
