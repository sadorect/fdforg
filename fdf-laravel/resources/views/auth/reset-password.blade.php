@extends('layouts.app')

@section('content')
<section class="bg-gradient-to-r from-blue-900 to-indigo-800 py-14 text-white">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold">Reset Password</h1>
        <p class="mt-2 text-blue-100">Set a new password for your account.</p>
    </div>
</section>

<section class="py-12">
    <div class="mx-auto max-w-xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="rounded-lg border border-gray-200 bg-slate-50 p-4">
                    <label for="reset-email" class="text-sm font-semibold text-gray-800">Email</label>
                    <input id="reset-email" type="email" name="email" value="{{ old('email', $email) }}" required autocomplete="email" class="mt-2 w-full rounded-md border-2 border-slate-300 bg-white px-3 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="rounded-lg border border-gray-200 bg-slate-50 p-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="reset-password" class="text-xs font-medium text-gray-700">New Password</label>
                            <input id="reset-password" type="password" name="password" required autocomplete="new-password" class="mt-1 w-full rounded-md border-2 border-slate-300 bg-white px-3 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                            @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="reset-password-confirmation" class="text-xs font-medium text-gray-700">Confirm Password</label>
                            <input id="reset-password-confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="mt-1 w-full rounded-md border-2 border-slate-300 bg-white px-3 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>
                </div>

                <div class="rounded-md border border-gray-200 bg-gray-50 p-4" data-captcha-block>
                    <label for="reset-captcha-answer" class="block text-sm font-medium text-gray-700">Math CAPTCHA: What is <span data-captcha-question>{{ $captchaQuestion }}</span>?</label>
                    <p class="sr-only" data-captcha-status aria-live="polite" aria-atomic="true"></p>
                    <div class="mt-2 flex items-center gap-3">
                        <input id="reset-captcha-answer" type="number" name="captcha_answer" value="{{ old('captcha_answer') }}" required data-captcha-input class="w-full rounded-md border-2 border-slate-300 bg-white px-3 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                        <button type="button" data-captcha-refresh data-refresh-url="{{ route('auth.captcha') }}" data-fallback-url="{{ route('password.reset', ['token' => $token, 'email' => old('email', $email), 'refresh_captcha' => 1]) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">New CAPTCHA</button>
                    </div>
                    @error('captcha_answer') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Reset Password</button>
            </form>
        </div>
    </div>
</section>
@endsection
