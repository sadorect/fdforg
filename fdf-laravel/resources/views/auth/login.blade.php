@extends('layouts.app')

@section('content')
<section class="bg-gradient-to-r from-slate-800 to-blue-800 py-14 text-white">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold">Sign In</h1>
        <p class="mt-2 text-blue-100">Access your learner dashboard, enrollments, and payments.</p>
    </div>
</section>

<section class="py-12">
    <div class="mx-auto max-w-xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <form method="POST" action="{{ route('login.submit') }}" class="space-y-4">
                @csrf

                <div class="rounded-lg border border-gray-200 bg-slate-50 p-4">
                    <label class="text-sm font-semibold text-gray-800">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required class="mt-2 w-full rounded-md border-2 border-slate-300 bg-white px-3 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="rounded-lg border border-gray-200 bg-slate-50 p-4">
                    <div class="flex items-center justify-between">
                        <label class="text-sm font-semibold text-gray-800">Password</label>
                        <a href="{{ route('password.request') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">Forgot password?</a>
                    </div>
                    <input type="password" name="password" required class="mt-2 w-full rounded-md border-2 border-slate-300 bg-white px-3 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="rounded-md border border-gray-200 bg-gray-50 p-4">
                    <label class="block text-sm font-medium text-gray-700">Math CAPTCHA: What is {{ $captchaQuestion }}?</label>
                    <div class="mt-2 flex items-center gap-3">
                        <input type="number" name="captcha_answer" value="{{ old('captcha_answer') }}" required class="w-full rounded-md border-2 border-slate-300 bg-white px-3 py-2 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                        <a href="{{ route('login', ['refresh_captcha' => 1]) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">New CAPTCHA</a>
                    </div>
                    @error('captcha_answer') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="w-full rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Sign In</button>
            </form>

            <p class="mt-4 text-sm text-gray-600">
                No account yet?
                <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-800">Create one</a>
            </p>
        </div>
    </div>
</section>
@endsection