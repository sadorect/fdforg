@extends('layouts.app')

@section('content')
<section class="bg-gradient-to-r from-blue-900 to-sky-700 py-14 text-white">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold">Create Account</h1>
        <p class="mt-2 text-blue-100">Set up your learner account to manage enrollments and payments.</p>
    </div>
</section>

<section class="py-12">
    <div class="mx-auto max-w-2xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <form method="POST" action="{{ route('register.submit') }}" class="space-y-4">
                @csrf

                <div class="rounded-lg border border-gray-200 bg-slate-50 p-4">
                    <label for="register-name" class="text-sm font-semibold text-gray-800">Full Name</label>
                    <input id="register-name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name" class="mt-2 w-full rounded-md border-2 border-slate-300 bg-white px-3 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500" placeholder="Jane Doe">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="rounded-lg border border-gray-200 bg-slate-50 p-4">
                    <label for="register-email" class="text-sm font-semibold text-gray-800">Email</label>
                    <input id="register-email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="mt-2 w-full rounded-md border-2 border-slate-300 bg-white px-3 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500" placeholder="you@example.com">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="rounded-lg border border-gray-200 bg-slate-50 p-4">
                    <p class="text-sm font-semibold text-gray-800">Security</p>
                    <div class="mt-3 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="register-password" class="text-xs font-medium text-gray-700">Password</label>
                            <input id="register-password" type="password" name="password" required autocomplete="new-password" class="mt-1 w-full rounded-md border-2 border-slate-300 bg-white px-3 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="register-password-confirmation" class="text-xs font-medium text-gray-700">Confirm Password</label>
                            <input id="register-password-confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="mt-1 w-full rounded-md border-2 border-slate-300 bg-white px-3 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <div class="rounded-md border border-gray-200 bg-gray-50 p-4" data-captcha-block>
                    <label for="register-captcha-answer" class="block text-sm font-medium text-gray-700">Math CAPTCHA: What is <span data-captcha-question>{{ $captchaQuestion }}</span>?</label>
                    <p class="sr-only" data-captcha-status aria-live="polite" aria-atomic="true"></p>
                    <div class="mt-2 flex items-center gap-3">
                        <input id="register-captcha-answer" type="number" name="captcha_answer" value="{{ old('captcha_answer') }}" required data-captcha-input class="w-full rounded-md border-2 border-slate-300 bg-white px-3 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                        <button type="button" data-captcha-refresh data-refresh-url="{{ route('auth.captcha') }}" data-fallback-url="{{ route('register', ['refresh_captcha' => 1]) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">New CAPTCHA</button>
                    </div>
                    @error('captcha_answer') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Create Account</button>
            </form>

            <p class="mt-4 text-sm text-gray-600">
                Already have an account?
                <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-800">Sign in</a>
            </p>
        </div>
    </div>
</section>
@endsection
