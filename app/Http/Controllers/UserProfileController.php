<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('dashboard.profile', [
            'user' => $request->user(),
            'learnerTypeOptions' => User::learnerTypeOptions(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'learner_type' => ['required', Rule::in(User::LEARNER_TYPES)],
            'location' => ['required', 'string', 'max:255'],
            'country' => ['required', 'string', 'max:120'],
            'phone_number' => ['nullable', 'string', 'max:50'],
            'organization_name' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
        ]);

        $user->forceFill([
            ...$validated,
            'learner_profile_completed_at' => now(),
            'learner_profile_deferred_at' => null,
        ])->save();

        return back()->with('success', 'Your learner profile has been updated.');
    }

    public function defer(Request $request): RedirectResponse
    {
        $request->user()->forceFill([
            'learner_profile_deferred_at' => now(),
        ])->save();

        return back()->with('info', 'No problem. We will remind you again after lesson completion.');
    }
}