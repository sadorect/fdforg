@extends('layouts.app')

@section('content')
<section class="bg-gradient-to-r from-indigo-900 to-blue-800 py-14 text-white">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold">Complete Payment</h1>
        <p class="mt-2 text-blue-100">{{ $enrollment->course->title }}</p>
    </div>
</section>

<section class="py-10">
    <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
            <div class="mb-5 rounded-md bg-blue-50 p-4 text-sm text-blue-800">
                <p class="font-semibold">Amount Due</p>
                <p class="text-2xl font-bold">{{ $enrollment->course->formatted_price }}</p>
            </div>

            <form method="POST" action="{{ route('dashboard.pay.submit', $enrollment->id) }}" class="space-y-4">
                @csrf

                <div>
                    <label class="text-sm font-medium text-gray-700">Cardholder Name</label>
                    <input type="text" name="card_name" value="{{ old('card_name') }}" required class="mt-1 w-full rounded-md border-gray-300">
                    @error('card_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Card Number</label>
                    <input type="text" name="card_number" value="{{ old('card_number') }}" required maxlength="16" class="mt-1 w-full rounded-md border-gray-300" placeholder="4242424242424242">
                    @error('card_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="text-sm font-medium text-gray-700">Expiry (MM/YY)</label>
                        <input type="text" name="expiry" value="{{ old('expiry') }}" required class="mt-1 w-full rounded-md border-gray-300" placeholder="12/30">
                        @error('expiry') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-700">CVV</label>
                        <input type="password" name="cvv" value="{{ old('cvv') }}" required class="mt-1 w-full rounded-md border-gray-300" maxlength="4" placeholder="123">
                        @error('cvv') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="text-sm font-medium text-gray-700">Math CAPTCHA: What is {{ $captchaQuestion }}?</label>
                    <div class="mt-1 flex items-center gap-3">
                        <input type="number" name="captcha_answer" value="{{ old('captcha_answer') }}" required class="w-full rounded-md border-gray-300">
                        <a href="{{ route('dashboard.pay', ['enrollment' => $enrollment->id, 'refresh_captcha' => 1]) }}" class="text-sm font-semibold text-blue-600 hover:text-blue-800">New CAPTCHA</a>
                    </div>
                    @error('captcha_answer') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex gap-2 pt-2">
                    <a href="{{ route('dashboard.payments') }}" class="rounded-md bg-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-300">Cancel</a>
                    <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Pay {{ $enrollment->course->formatted_price }}</button>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
