@extends('layouts.app')

@section('content')
<section class="bg-gradient-to-r from-slate-900 to-cyan-800 py-14 text-white">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold">Learner Profile</h1>
        <p class="mt-2 max-w-2xl text-sm text-cyan-100">Help us understand who we are reaching across our learning programs. You can update these details at any time.</p>
    </div>
</section>

<section class="py-10">
    <div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8">
        <div class="rounded-3xl bg-white p-6 shadow-sm ring-1 ring-gray-200 md:p-8">
            <form method="POST" action="{{ route('dashboard.profile.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid gap-6 md:grid-cols-2">
                    <div>
                        <label for="profile-name" class="text-sm font-medium text-gray-700">Full Name</label>
                        <input id="profile-name" type="text" name="name" value="{{ old('name', $user->name) }}" required class="mt-2 w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900 focus:border-cyan-500 focus:ring-cyan-500">
                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="profile-learner-type" class="text-sm font-medium text-gray-700">Learner Type</label>
                        <select id="profile-learner-type" name="learner_type" required class="mt-2 w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900 focus:border-cyan-500 focus:ring-cyan-500">
                            <option value="">Select learner type</option>
                            @foreach($learnerTypeOptions as $value => $label)
                                <option value="{{ $value }}" @selected(old('learner_type', $user->learner_type) === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('learner_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="profile-location" class="text-sm font-medium text-gray-700">Location / City</label>
                        <input id="profile-location" type="text" name="location" value="{{ old('location', $user->location) }}" required class="mt-2 w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900 focus:border-cyan-500 focus:ring-cyan-500">
                        @error('location') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="profile-country" class="text-sm font-medium text-gray-700">Country</label>
                        <input id="profile-country" type="text" name="country" value="{{ old('country', $user->country) }}" required class="mt-2 w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900 focus:border-cyan-500 focus:ring-cyan-500">
                        @error('country') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="profile-phone" class="text-sm font-medium text-gray-700">Phone Number</label>
                        <input id="profile-phone" type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" class="mt-2 w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900 focus:border-cyan-500 focus:ring-cyan-500">
                        @error('phone_number') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="profile-organization" class="text-sm font-medium text-gray-700">Organisation / School</label>
                        <input id="profile-organization" type="text" name="organization_name" value="{{ old('organization_name', $user->organization_name) }}" class="mt-2 w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900 focus:border-cyan-500 focus:ring-cyan-500">
                        @error('organization_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label for="profile-bio" class="text-sm font-medium text-gray-700">Short Bio</label>
                    <textarea id="profile-bio" name="bio" rows="4" class="mt-2 w-full rounded-xl border border-gray-300 bg-gray-50 px-4 py-3 text-gray-900 focus:border-cyan-500 focus:ring-cyan-500">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="rounded-full bg-cyan-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-cyan-700">Save Learner Profile</button>
                    <a href="{{ route('dashboard') }}" class="rounded-full border border-slate-300 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-50">Back to Dashboard</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection