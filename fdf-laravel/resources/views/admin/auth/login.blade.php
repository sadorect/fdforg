<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Friends of the Deaf Foundation</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <a href="#admin-login-main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:rounded-md focus:bg-gray-900 focus:px-4 focus:py-2 focus:text-white">Skip to main content</a>
    <main id="admin-login-main" class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <div class="mx-auto h-12 w-12 flex items-center justify-center rounded-full bg-indigo-100">
                    <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                </div>
                <h2 class="mt-6 text-center text-3xl font-extrabold text-gray-900">Admin Login</h2>
                <p class="mt-2 text-center text-sm text-gray-600">Friends of the Deaf Foundation</p>
            </div>

            <form class="mt-8 space-y-6" action="{{ route('admin.login.submit') }}" method="POST">
                @csrf

                @if ($errors->any())
                    <div class="rounded-md bg-red-50 p-4" role="alert" aria-live="assertive">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">There was an error with your submission</h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <ul class="list-disc pl-5 space-y-1">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="rounded-md shadow-sm -space-y-px">
                    <div>
                        <label for="email-address" class="sr-only">Email address</label>
                        <input id="email-address" name="email" type="email" autocomplete="email" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-t-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Email address" value="{{ old('email') }}">
                    </div>
                    <div>
                        <label for="password" class="sr-only">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" required class="appearance-none rounded-none relative block w-full px-3 py-2 border border-gray-300 placeholder-gray-500 text-gray-900 rounded-b-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 focus:z-10 sm:text-sm" placeholder="Password">
                    </div>
                </div>

                <div class="rounded-md border border-gray-200 bg-white p-4" data-admin-captcha-block>
                    <label for="captcha_answer" class="block text-sm font-medium text-gray-700">
                        Math CAPTCHA: What is <span data-admin-captcha-question>{{ $captchaQuestion ?? '0 + 0' }}</span>?
                    </label>
                    <p class="sr-only" data-admin-captcha-status aria-live="polite" aria-atomic="true"></p>
                    <div class="mt-2 flex items-center gap-3">
                        <input
                            id="captcha_answer"
                            name="captcha_answer"
                            type="number"
                            required
                            data-admin-captcha-input
                            class="w-full rounded-md border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                            placeholder="Enter answer"
                            value="{{ old('captcha_answer') }}"
                        >
                        <button type="button" data-admin-captcha-refresh data-refresh-url="{{ route('admin.captcha') }}" data-fallback-url="{{ route('admin.login', ['refresh_captcha' => 1]) }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
                            New CAPTCHA
                        </button>
                    </div>
                    @error('captcha_answer') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <button type="submit" class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <svg class="h-5 w-5 text-indigo-500 group-hover:text-indigo-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </span>
                        Sign in
                    </button>
                </div>

                <div class="text-center">
                    <a href="{{ route('home') }}" class="text-sm text-gray-600 hover:text-gray-900">
                        &larr; Back to website
                    </a>
                </div>
            </form>
        </div>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const refreshButton = document.querySelector('[data-admin-captcha-refresh]');
            const questionNode = document.querySelector('[data-admin-captcha-question]');
            const answerInput = document.querySelector('[data-admin-captcha-input]');
            const statusNode = document.querySelector('[data-admin-captcha-status]');

            if (!refreshButton || !questionNode) {
                return;
            }

            refreshButton.addEventListener('click', async function () {
                const refreshUrl = refreshButton.getAttribute('data-refresh-url');
                const fallbackUrl = refreshButton.getAttribute('data-fallback-url');
                const originalLabel = refreshButton.textContent;

                refreshButton.disabled = true;
                refreshButton.textContent = 'Refreshing...';

                if (statusNode) {
                    statusNode.textContent = 'Refreshing the CAPTCHA question.';
                }

                try {
                    const response = await fetch(refreshUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        credentials: 'same-origin',
                        cache: 'no-store',
                    });

                    if (!response.ok) {
                        throw new Error('Failed to refresh CAPTCHA.');
                    }

                    const payload = await response.json();

                    if (!payload.question) {
                        throw new Error('Captcha question missing.');
                    }

                    questionNode.textContent = payload.question;

                    if (answerInput) {
                        answerInput.value = '';
                        answerInput.focus();
                    }

                    if (statusNode) {
                        statusNode.textContent = 'New CAPTCHA question loaded: What is ' + payload.question + '?';
                    }
                } catch (error) {
                    if (statusNode) {
                        statusNode.textContent = 'Refreshing the CAPTCHA failed. Reloading the page now.';
                    }

                    window.location.href = fallbackUrl;
                } finally {
                    refreshButton.disabled = false;
                    refreshButton.textContent = originalLabel;
                }
            });
        });
    </script>
</body>
</html>
