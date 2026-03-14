@extends('layouts.admin')

@section('title', 'Admin Profile')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Admin Profile</h1>
        <p class="text-sm text-gray-600">Manage your account details and password.</p>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <section class="rounded-lg bg-white p-6 shadow">
            <h2 class="text-lg font-semibold text-gray-900">Profile Details</h2>
            <form method="POST" action="{{ route('admin.profile.update') }}" class="mt-4 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="admin-profile-name" class="text-sm font-medium text-gray-700">Full Name</label>
                    <input id="admin-profile-name" type="text" name="name" value="{{ old('name', $user->name) }}" autocomplete="name" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="admin-profile-email" class="text-sm font-medium text-gray-700">Email Address</label>
                    <input id="admin-profile-email" type="email" name="email" value="{{ old('email', $user->email) }}" autocomplete="email" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="admin-profile-bio" class="text-sm font-medium text-gray-700">Bio</label>
                    <textarea id="admin-profile-bio" name="bio" rows="4" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">{{ old('bio', $user->bio) }}</textarea>
                    @error('bio') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <button type="submit" class="rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">Save Profile</button>
            </form>
        </section>

        <section class="rounded-lg bg-white p-6 shadow">
            <h2 class="text-lg font-semibold text-gray-900">Change Password</h2>
            <form method="POST" action="{{ route('admin.profile.password') }}" class="mt-4 space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label for="admin-profile-current-password" class="text-sm font-medium text-gray-700">Current Password</label>
                    <input id="admin-profile-current-password" type="password" name="current_password" autocomplete="current-password" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    @error('current_password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="admin-profile-password" class="text-sm font-medium text-gray-700">New Password</label>
                    <input id="admin-profile-password" type="password" name="password" autocomplete="new-password" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    @error('password') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="admin-profile-password-confirmation" class="text-sm font-medium text-gray-700">Confirm New Password</label>
                    <input id="admin-profile-password-confirmation" type="password" name="password_confirmation" autocomplete="new-password" class="mt-1 w-full rounded-md border-gray-300 bg-gray-50 text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                </div>
                <button type="submit" class="rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white hover:bg-gray-900">Update Password</button>
            </form>
        </section>
    </div>
</div>
@endsection
