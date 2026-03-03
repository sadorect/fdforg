<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use App\Support\MathCaptcha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $enrollments = Enrollment::with(['course', 'course.instructor'])
            ->where('user_id', $request->user()->id)
            ->orderByDesc('enrolled_at')
            ->get();

        $stats = [
            'total' => $enrollments->count(),
            'active' => $enrollments->where('status', 'active')->count(),
            'completed' => $enrollments->where('status', 'completed')->count(),
            'pending_payments' => $enrollments->where('payment_status', 'pending')->count(),
        ];

        return view('dashboard.index', compact('enrollments', 'stats'));
    }

    public function payments(Request $request): View
    {
        $pendingEnrollments = Enrollment::with('course')
            ->where('user_id', $request->user()->id)
            ->where('payment_status', 'pending')
            ->orderByDesc('enrolled_at')
            ->get();

        return view('dashboard.payments', compact('pendingEnrollments'));
    }

    public function showPayment(Request $request, Enrollment $enrollment): View
    {
        $this->authorizeEnrollment($request, $enrollment);

        $enrollment->load('course');
        MathCaptcha::ensure($request, 'payment_action');

        return view('dashboard.pay', [
            'enrollment' => $enrollment,
            'captchaQuestion' => MathCaptcha::question($request, 'payment_action'),
        ]);
    }

    public function processPayment(Request $request, Enrollment $enrollment): RedirectResponse
    {
        $this->authorizeEnrollment($request, $enrollment);

        $request->validate([
            'card_name' => ['required', 'string', 'max:255'],
            'card_number' => ['required', 'digits:16'],
            'expiry' => ['required', 'regex:/^(0[1-9]|1[0-2])\\/[0-9]{2}$/'],
            'cvv' => ['required', 'digits_between:3,4'],
            'captcha_answer' => ['required', 'integer'],
        ]);

        if (!MathCaptcha::isValid($request, 'payment_action')) {
            MathCaptcha::regenerate($request, 'payment_action');

            return back()
                ->withErrors(['captcha_answer' => 'Incorrect math captcha answer. Please try again.'])
                ->withInput($request->except('captcha_answer', 'cvv'));
        }

        $enrollment->update([
            'payment_status' => 'paid',
            'paid_amount' => $enrollment->course->price,
            'currency_code' => $enrollment->course->currency_code,
            'status' => $enrollment->status === 'cancelled' ? 'active' : $enrollment->status,
        ]);
        MathCaptcha::regenerate($request, 'payment_action');

        return redirect()
            ->route('dashboard')
            ->with('success', 'Payment successful. Your enrollment is now fully active.');
    }

    public function continueLearning(Request $request, Enrollment $enrollment): RedirectResponse
    {
        $this->authorizeEnrollment($request, $enrollment);

        if ($enrollment->payment_status !== 'paid') {
            return redirect()
                ->route('dashboard.pay', $enrollment->id)
                ->with('info', 'Complete payment to continue learning.');
        }

        $enrollment->load('course.publishedLessons');

        $lesson = $enrollment->course->publishedLessons
            ->first(function ($item) use ($enrollment) {
                return $enrollment->canAccessLesson($item);
            });

        if (!$lesson) {
            return redirect()
                ->route('courses.show', $enrollment->course->slug)
                ->with('info', 'No published lessons are currently available.');
        }

        return redirect()->route('courses.lessons.show', [$enrollment->course->slug, $lesson->slug]);
    }

    private function authorizeEnrollment(Request $request, Enrollment $enrollment): void
    {
        abort_unless($enrollment->user_id === $request->user()->id, 403);
    }
}
