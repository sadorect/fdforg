<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicCourseEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function test_published_course_is_visible_on_public_routes(): void
    {
        $instructor = User::factory()->create(['is_admin' => true]);
        $course = Course::create([
            'title' => 'Public ASL Intro',
            'slug' => 'public-asl-intro',
            'description' => 'A public intro ASL course.',
            'instructor_id' => $instructor->id,
            'status' => 'published',
            'difficulty_level' => 'beginner',
            'duration_minutes' => 90,
            'price' => 0,
        ]);

        $this->get(route('courses.index'))
            ->assertOk()
            ->assertSee('Learning opportunities built for deaf learners, families, and allies.')
            ->assertSee('Public ASL Intro');

        $this->get(route('courses.show', $course->slug))
            ->assertOk()
            ->assertSee('Learning Path')
            ->assertSee('Sign In');
    }

    public function test_legacy_courses_urls_redirect_to_learning_routes(): void
    {
        $instructor = User::factory()->create(['is_admin' => true]);
        $course = Course::create([
            'title' => 'Legacy Redirect Course',
            'slug' => 'legacy-redirect-course',
            'description' => 'Legacy redirect course.',
            'instructor_id' => $instructor->id,
            'status' => 'published',
            'difficulty_level' => 'beginner',
            'duration_minutes' => 90,
            'price' => 0,
        ]);

        $this->get('/courses')->assertRedirect(route('courses.index'));
        $this->get('/courses/'.$course->slug)->assertRedirect(route('courses.show', $course->slug));
    }

    public function test_guest_must_sign_in_before_enrollment(): void
    {
        $instructor = User::factory()->create(['is_admin' => true]);
        $course = Course::create([
            'title' => 'Free Basics',
            'slug' => 'free-basics',
            'description' => 'Free course',
            'instructor_id' => $instructor->id,
            'status' => 'published',
            'difficulty_level' => 'beginner',
            'duration_minutes' => 60,
            'price' => 0,
        ]);

        $response = $this->post(route('courses.enroll', $course->slug));

        $response->assertRedirect('/login');
        $this->assertDatabaseCount('enrollments', 0);
    }

    public function test_authenticated_user_can_enroll_in_free_course(): void
    {
        $instructor = User::factory()->create(['is_admin' => true]);
        $learner = User::factory()->create(['is_admin' => false]);
        $course = Course::create([
            'title' => 'Free Basics',
            'slug' => 'free-basics',
            'description' => 'Free course',
            'instructor_id' => $instructor->id,
            'status' => 'published',
            'difficulty_level' => 'beginner',
            'duration_minutes' => 60,
            'price' => 0,
        ]);

        $response = $this->actingAs($learner)
            ->withSession([
                'course_enroll_captcha_question' => '3 + 4',
                'course_enroll_captcha_answer' => 7,
            ])
            ->post(route('courses.enroll', $course->slug), [
                'captcha_answer' => 7,
            ]);
        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('enrollments', [
            'course_id' => $course->id,
            'user_id' => $learner->id,
            'payment_status' => 'paid',
        ]);
    }

    public function test_authenticated_user_is_redirected_to_payment_for_paid_course(): void
    {
        $instructor = User::factory()->create(['is_admin' => true]);
        $learner = User::factory()->create(['is_admin' => false]);
        $course = Course::create([
            'title' => 'Paid Advanced',
            'slug' => 'paid-advanced',
            'description' => 'Paid course',
            'instructor_id' => $instructor->id,
            'status' => 'published',
            'difficulty_level' => 'advanced',
            'duration_minutes' => 180,
            'price' => 150,
        ]);

        $response = $this->actingAs($learner)
            ->withSession([
                'course_enroll_captcha_question' => '2 + 6',
                'course_enroll_captcha_answer' => 8,
            ])
            ->post(route('courses.enroll', $course->slug), [
                'captcha_answer' => 8,
            ]);

        $enrollment = Enrollment::where('course_id', $course->id)
            ->where('user_id', $learner->id)
            ->firstOrFail();

        $response->assertRedirect('/dashboard/payments/'.$enrollment->id);

        $this->assertDatabaseHas('enrollments', [
            'course_id' => $course->id,
            'user_id' => $learner->id,
            'payment_status' => 'pending',
            'paid_amount' => 0,
        ]);
    }

    public function test_duplicate_public_enrollment_is_blocked(): void
    {
        $instructor = User::factory()->create(['is_admin' => true]);
        $learner = User::factory()->create(['email' => 'dupe@example.com']);
        $course = Course::create([
            'title' => 'Duplicate Guard',
            'slug' => 'duplicate-guard',
            'description' => 'Duplicate test',
            'instructor_id' => $instructor->id,
            'status' => 'published',
            'difficulty_level' => 'beginner',
            'duration_minutes' => 45,
            'price' => 0,
        ]);

        Enrollment::create([
            'course_id' => $course->id,
            'user_id' => $learner->id,
            'status' => 'active',
            'enrolled_at' => now(),
            'progress_percentage' => 0,
            'payment_status' => 'paid',
            'paid_amount' => 0,
        ]);

        $this->actingAs($learner)
            ->withSession([
                'course_enroll_captcha_question' => '5 + 2',
                'course_enroll_captcha_answer' => 7,
            ])
            ->post(route('courses.enroll', $course->slug), [
                'captcha_answer' => 7,
            ])
            ->assertRedirect('/dashboard');

        $this->assertSame(1, Enrollment::where('course_id', $course->id)->count());
    }
}
