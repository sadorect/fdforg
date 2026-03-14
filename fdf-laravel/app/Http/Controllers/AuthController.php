<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\TemplateEmailService;
use App\Support\MathCaptcha;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function refreshCaptcha(Request $request): JsonResponse
    {
        MathCaptcha::regenerate($request, 'user_auth');

        return response()
            ->json(['question' => MathCaptcha::question($request, 'user_auth')])
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function showLoginForm(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        MathCaptcha::ensure($request, 'user_auth');

        return view('auth.login', [
            'captchaQuestion' => MathCaptcha::question($request, 'user_auth'),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
            'captcha_answer' => ['required', 'integer'],
        ]);

        if (! MathCaptcha::isValid($request, 'user_auth')) {
            MathCaptcha::regenerate($request, 'user_auth');

            return back()
                ->withErrors(['captcha_answer' => 'Incorrect math captcha answer. Please try again.'])
                ->withInput($request->except('password', 'captcha_answer'));
        }

        if (! Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            MathCaptcha::regenerate($request, 'user_auth');

            return back()
                ->withErrors(['email' => 'The provided credentials do not match our records.'])
                ->withInput($request->except('password', 'captcha_answer'));
        }

        $request->session()->regenerate();
        MathCaptcha::regenerate($request, 'user_auth');

        return redirect()->intended(route('dashboard'));
    }

    public function showRegisterForm(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        MathCaptcha::ensure($request, 'user_auth');

        return view('auth.register', [
            'captchaQuestion' => MathCaptcha::question($request, 'user_auth'),
        ]);
    }

    public function register(Request $request, TemplateEmailService $templateEmailService): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'captcha_answer' => ['required', 'integer'],
        ]);

        if (! MathCaptcha::isValid($request, 'user_auth')) {
            MathCaptcha::regenerate($request, 'user_auth');

            return back()
                ->withErrors(['captcha_answer' => 'Incorrect math captcha answer. Please try again.'])
                ->withInput($request->except('password', 'password_confirmation', 'captcha_answer'));
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'email_verified_at' => now(),
            'is_admin' => false,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        MathCaptcha::regenerate($request, 'user_auth');

        try {
            $templateEmailService->send('user_registration_welcome', $user->email, [
                'user_name' => $user->name,
                'courses_url' => route('courses.index'),
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Unable to send user registration welcome email.', [
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Account created successfully.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    public function showForgotPasswordForm(Request $request): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        MathCaptcha::ensure($request, 'user_auth');

        return view('auth.forgot-password', [
            'captchaQuestion' => MathCaptcha::question($request, 'user_auth'),
        ]);
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'captcha_answer' => ['required', 'integer'],
        ]);

        if (! MathCaptcha::isValid($request, 'user_auth')) {
            MathCaptcha::regenerate($request, 'user_auth');

            return back()
                ->withErrors(['captcha_answer' => 'Incorrect math captcha answer. Please try again.'])
                ->withInput($request->except('captcha_answer'));
        }

        $status = Password::sendResetLink($request->only('email'));
        MathCaptcha::regenerate($request, 'user_auth');

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', __($status));
        }

        return back()
            ->withErrors(['email' => __($status)])
            ->withInput($request->only('email'));
    }

    public function showResetPasswordForm(Request $request, string $token): View|RedirectResponse
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        MathCaptcha::ensure($request, 'user_auth');

        return view('auth.reset-password', [
            'token' => $token,
            'email' => (string) $request->query('email', ''),
            'captchaQuestion' => MathCaptcha::question($request, 'user_auth'),
        ]);
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'captcha_answer' => ['required', 'integer'],
        ]);

        if (! MathCaptcha::isValid($request, 'user_auth')) {
            MathCaptcha::regenerate($request, 'user_auth');

            return back()
                ->withErrors(['captcha_answer' => 'Incorrect math captcha answer. Please try again.'])
                ->withInput($request->except('password', 'password_confirmation', 'captcha_answer'));
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        MathCaptcha::regenerate($request, 'user_auth');

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', __($status));
        }

        return back()
            ->withErrors(['email' => __($status)])
            ->withInput($request->except('password', 'password_confirmation', 'captcha_answer'));
    }
}
