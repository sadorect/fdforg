<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Services\TemplateEmailService;
use App\Support\MathCaptcha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class EventController extends Controller
{
    /**
     * Display a listing of events.
     */
    public function index(): View
    {
        $events = Event::upcoming()
            ->withCount('registrations')
            ->paginate(9);
        $pastEvents = Event::past()->take(5)->get();

        return view('events.index', compact('events', 'pastEvents'));
    }

    /**
     * Display the specified event.
     */
    public function show(string $slug): View
    {
        $event = Event::where('slug', $slug)
            ->withCount('registrations')
            ->firstOrFail();
        
        // Get related events (upcoming events excluding current one)
        $relatedEvents = Event::upcoming()
                             ->where('id', '!=', $event->id)
                             ->take(3)
                             ->get();

        return view('events.show', compact('event', 'relatedEvents'));
    }

    /**
     * Display calendar view of events.
     */
    public function calendar(): View
    {
        $events = Event::upcoming()->get();
        
        return view('events.calendar', compact('events'));
    }

    /**
     * Handle event registration redirect.
     */
    public function register(Request $request, string $slug): View|RedirectResponse
    {
        $event = Event::where('slug', $slug)->withCount('registrations')->firstOrFail();

        if (!$event->registration_required) {
            return redirect()
                ->route('events.show', $event->slug)
                ->with('info', 'This event does not require registration.');
        }

        if (!$event->hasAvailableCapacity()) {
            return redirect()
                ->route('events.show', $event->slug)
                ->withErrors(['registration' => 'Registration is closed because this event has reached capacity.']);
        }

        $existingRegistration = null;
        if ($request->user()) {
            $existingRegistration = EventRegistration::query()
                ->where('event_id', $event->id)
                ->where('email', $request->user()->email)
                ->exists();
        }

        if ($existingRegistration) {
            return redirect()
                ->route('events.show', $event->slug)
                ->with('info', 'You are already registered for this event.');
        }

        MathCaptcha::ensure($request, 'event_registration');

        return view('events.register', [
            'event' => $event,
            'captchaQuestion' => MathCaptcha::question($request, 'event_registration'),
        ]);
    }

    public function submitRegistration(
        Request $request,
        string $slug,
        TemplateEmailService $templateEmailService
    ): RedirectResponse {
        $event = Event::where('slug', $slug)->withCount('registrations')->firstOrFail();

        if (!$event->registration_required) {
            return redirect()
                ->route('events.show', $event->slug)
                ->with('info', 'This event does not require registration.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'captcha_answer' => ['required', 'integer'],
        ]);

        if (!MathCaptcha::isValid($request, 'event_registration')) {
            MathCaptcha::regenerate($request, 'event_registration');

            return back()
                ->withErrors(['captcha_answer' => 'Incorrect math captcha answer. Please try again.'])
                ->withInput($request->except('captcha_answer'));
        }

        if (!$event->hasAvailableCapacity()) {
            return back()->withErrors([
                'registration' => 'Registration is closed because this event has reached capacity.',
            ])->withInput();
        }

        $existingRegistration = EventRegistration::query()
            ->where('event_id', $event->id)
            ->where('email', $validated['email'])
            ->exists();

        if ($existingRegistration) {
            return back()
                ->withErrors(['email' => 'This email is already registered for the event.'])
                ->withInput();
        }

        EventRegistration::create([
            'event_id' => $event->id,
            'user_id' => $request->user()?->id,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'status' => 'registered',
            'registered_at' => now(),
        ]);

        try {
            $templateEmailService->send('event_registration_confirmation', $validated['email'], [
                'registrant_name' => $validated['name'],
                'event_title' => $event->title,
                'event_date' => $event->getFormattedDateRange(),
                'event_time' => $event->time ?: 'TBD',
                'event_location' => $event->getDisplayLocation(),
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Unable to send event registration email.', [
                'event_id' => $event->id,
                'email' => $validated['email'],
                'error' => $exception->getMessage(),
            ]);
        }

        MathCaptcha::regenerate($request, 'event_registration');

        return redirect()
            ->route('events.show', $event->slug)
            ->with('success', 'Registration completed successfully. A confirmation email has been sent.');
    }
}
