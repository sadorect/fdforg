<?php

namespace Tests\Feature;

use App\Mail\TemplateNotificationMail;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CurrencyAndEnrollmentEmailTest extends TestCase
{
    use RefreshDatabase;

    public function test_paid_course_enrollment_uses_ngn_currency_and_sends_instruction_email(): void
    {
        Mail::fake();

        $instructor = User::factory()->create(['is_admin' => true]);
        $learner = User::factory()->create(['is_admin' => false]);

        $course = Course::create([
            'title' => 'Nigerian Sign Language Essentials',
            'slug' => 'nigerian-sign-language-essentials',
            'description' => 'Foundational sign language course.',
            'instructor_id' => $instructor->id,
            'status' => 'published',
            'difficulty_level' => 'beginner',
            'duration_minutes' => 120,
            'price' => 15000.00,
            'currency_code' => 'NGN',
        ]);

        $response = $this->actingAs($learner)
            ->withSession([
                'course_enroll_captcha_question' => '4 + 4',
                'course_enroll_captcha_answer' => 8,
            ])
            ->post(route('courses.enroll', $course->slug), [
                'captcha_answer' => 8,
            ]);

        $enrollment = Enrollment::query()
            ->where('course_id', $course->id)
            ->where('user_id', $learner->id)
            ->firstOrFail();

        $response->assertRedirect('/dashboard/payments/'.$enrollment->id);

        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'currency_code' => 'NGN',
            'payment_status' => 'pending',
        ]);

        Mail::assertSent(TemplateNotificationMail::class, function (TemplateNotificationMail $mail) use ($learner, $course) {
            return $mail->hasTo($learner->email)
                && str_contains($mail->subjectLine, $course->title)
                && str_contains($mail->body, $course->formatted_price)
                && str_contains($mail->body, 'bank transfer');
        });
    }
}
