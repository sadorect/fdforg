<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Services\TemplateEmailService;
use App\Support\MathCaptcha;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(): View
    {
        $courses = Course::with(['category', 'instructor'])
            ->published()
            ->orderByDesc('is_featured')
            ->orderBy('title')
            ->paginate(12);

        return view('courses.index', compact('courses'));
    }

    public function show(Request $request, Course $course): View
    {
        if (!$course->isPublished()) {
            abort(404);
        }

        $course->load([
            'category',
            'instructor',
            'publishedLessons',
        ]);

        $relatedCourses = Course::published()
            ->where('id', '!=', $course->id)
            ->when($course->category_id, function ($query) use ($course) {
                $query->where('category_id', $course->category_id);
            })
            ->take(3)
            ->get();

        $currentEnrollment = null;
        if (auth()->check()) {
            $currentEnrollment = Enrollment::where('course_id', $course->id)
                ->where('user_id', auth()->id())
                ->first();

            MathCaptcha::ensure($request, 'course_enroll');
        }

        return view('courses.show', [
            'course' => $course,
            'relatedCourses' => $relatedCourses,
            'currentEnrollment' => $currentEnrollment,
            'captchaQuestion' => auth()->check() ? MathCaptcha::question($request, 'course_enroll') : null,
        ]);
    }

    public function enroll(Request $request, Course $course, TemplateEmailService $templateEmailService): RedirectResponse
    {
        if (!$course->isPublished()) {
            abort(404);
        }

        if (!$request->user()) {
            return redirect()
                ->route('login')
                ->with('info', 'Sign in or create an account to enroll in courses.');
        }

        $request->validate([
            'captcha_answer' => ['required', 'integer'],
        ]);

        if (!MathCaptcha::isValid($request, 'course_enroll')) {
            MathCaptcha::regenerate($request, 'course_enroll');

            return back()
                ->withErrors(['captcha_answer' => 'Incorrect math captcha answer. Please try again.'])
                ->withInput($request->except('captcha_answer'));
        }

        if (!$course->hasCapacity()) {
            return back()->withErrors([
                'enrollment' => 'This course is currently at full capacity.',
            ])->withInput();
        }

        $user = $request->user();

        $existingEnrollment = Enrollment::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->first();

        if ($existingEnrollment) {
            return redirect()->route('dashboard')->with('info', 'You are already enrolled in this course.');
        }

        $enrollment = Enrollment::create([
            'course_id' => $course->id,
            'user_id' => $user->id,
            'status' => 'active',
            'enrolled_at' => now(),
            'progress_percentage' => 0,
            'payment_status' => $course->isFree() ? 'paid' : 'pending',
            'paid_amount' => 0,
            'currency_code' => $course->currency_code,
        ]);

        $course->incrementEnrollmentCount();

        $paymentInstructions = $course->isFree()
            ? 'No payment is required for this course.'
            : ($templateEmailService->renderTemplateBody('course_payment_instructions', [
                'payment_status' => ucfirst($enrollment->payment_status),
                'support_email' => config('mail.from.address', 'support@example.com'),
                'course_price' => $course->formatted_price,
            ]) ?? 'Please complete payment from your learner dashboard to unlock all lessons.');

        try {
            $templateEmailService->send('course_enrollment_confirmation', $user->email, [
                'user_name' => $user->name,
                'course_title' => $course->title,
                'course_price' => $course->formatted_price,
                'payment_status' => ucfirst($enrollment->payment_status),
                'payment_instructions' => $paymentInstructions,
                'dashboard_url' => route('dashboard'),
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Unable to send enrollment email.', [
                'course_id' => $course->id,
                'user_id' => $user->id,
                'error' => $exception->getMessage(),
            ]);
        }

        if ($course->isFree()) {
            MathCaptcha::regenerate($request, 'course_enroll');
            return redirect()
                ->route('dashboard')
                ->with('success', 'Enrollment successful. You can start learning now.');
        }

        MathCaptcha::regenerate($request, 'course_enroll');

        return redirect()
            ->route('dashboard.pay', $enrollment->id)
            ->with('info', 'Enrollment created. Complete payment to unlock full access.');
    }

    public function showLesson(Request $request, Course $course, Lesson $lesson): View|RedirectResponse
    {
        if (!$course->isPublished() || !$lesson->is_published || $lesson->course_id !== $course->id) {
            abort(404);
        }

        $user = $request->user();
        $enrollment = $user
            ? Enrollment::where('course_id', $course->id)->where('user_id', $user->id)->first()
            : null;

        if (!$lesson->is_free) {
            if (!$user) {
                return redirect()
                    ->route('login')
                    ->with('info', 'Sign in to access this lesson.');
            }

            if (!$enrollment || !in_array($enrollment->status, ['active', 'completed'], true)) {
                return redirect()
                    ->route('courses.show', $course->slug)
                    ->withErrors(['lesson' => 'You are not eligible to access this lesson yet.']);
            }

            if ($enrollment->payment_status !== 'paid') {
                return redirect()
                    ->route('dashboard.pay', $enrollment->id)
                    ->with('info', 'Complete payment to unlock lesson access.');
            }
        }

        $course->load(['publishedLessons']);

        if ($user && $enrollment && $enrollment->payment_status === 'paid') {
            MathCaptcha::ensure($request, 'lesson_complete');
        }

        $previousLesson = $course->publishedLessons
            ->where('sort_order', '<', $lesson->sort_order)
            ->sortByDesc('sort_order')
            ->first();

        $nextLesson = $course->publishedLessons
            ->where('sort_order', '>', $lesson->sort_order)
            ->sortBy('sort_order')
            ->first();

        $progress = null;
        if ($user && $enrollment) {
            $progress = $lesson->lessonProgress()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'lesson_id' => $lesson->id,
                    'enrollment_id' => $enrollment->id,
                ],
                [
                    'is_completed' => false,
                    'completion_percentage' => 0,
                    'watch_time_seconds' => 0,
                    'started_at' => now(),
                    'last_accessed_at' => now(),
                ]
            );

            $progress->update([
                'started_at' => $progress->started_at ?? now(),
                'last_accessed_at' => now(),
            ]);

            $enrollment->update(['last_accessed_at' => now()]);
        }

        return view('courses.lesson', [
            'course' => $course,
            'lesson' => $lesson,
            'enrollment' => $enrollment,
            'progress' => $progress,
            'previousLesson' => $previousLesson,
            'nextLesson' => $nextLesson,
            'captchaQuestion' => ($user && $enrollment && $enrollment->payment_status === 'paid')
                ? MathCaptcha::question($request, 'lesson_complete')
                : null,
        ]);
    }

    public function completeLesson(Request $request, Course $course, Lesson $lesson): RedirectResponse
    {
        if (!$course->isPublished() || !$lesson->is_published || $lesson->course_id !== $course->id) {
            abort(404);
        }

        $user = $request->user();
        if (!$user) {
            return redirect()->route('login');
        }

        $enrollment = Enrollment::where('course_id', $course->id)
            ->where('user_id', $user->id)
            ->first();

        if (
            !$enrollment
            || !in_array($enrollment->status, ['active', 'completed'], true)
            || !$enrollment->canAccessLesson($lesson)
            || $enrollment->payment_status !== 'paid'
        ) {
            return redirect()
                ->route('courses.show', $course->slug)
                ->withErrors(['lesson' => 'You are not eligible to complete this lesson.']);
        }

        $request->validate([
            'captcha_answer' => ['required', 'integer'],
        ]);

        if (!MathCaptcha::isValid($request, 'lesson_complete')) {
            MathCaptcha::regenerate($request, 'lesson_complete');

            return back()
                ->withErrors(['captcha_answer' => 'Incorrect math captcha answer. Please try again.'])
                ->withInput($request->except('captcha_answer'));
        }

        $lesson->markAsCompleted($user);
        MathCaptcha::regenerate($request, 'lesson_complete');

        return redirect()
            ->route('courses.lessons.show', [$course->slug, $lesson->slug])
            ->with('success', 'Lesson marked as completed.');
    }
}
