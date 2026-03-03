<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseLessonAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_paid_enrolled_user_can_access_paid_lesson_content(): void
    {
        [$course, $lesson, $learner] = $this->createPaidCourseWithLesson();

        Enrollment::create([
            'user_id' => $learner->id,
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
            'progress_percentage' => 0,
            'payment_status' => 'paid',
            'paid_amount' => $course->price,
        ]);

        $this->actingAs($learner)
            ->get("/courses/{$course->slug}/lessons/{$lesson->slug}")
            ->assertOk()
            ->assertSee('Lesson Content')
            ->assertSee('Paid lesson body');
    }

    public function test_pending_payment_user_is_redirected_to_payment_for_paid_lesson(): void
    {
        [$course, $lesson, $learner] = $this->createPaidCourseWithLesson();

        $enrollment = Enrollment::create([
            'user_id' => $learner->id,
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
            'progress_percentage' => 0,
            'payment_status' => 'pending',
            'paid_amount' => 0,
        ]);

        $this->actingAs($learner)
            ->get("/courses/{$course->slug}/lessons/{$lesson->slug}")
            ->assertRedirect("/dashboard/payments/{$enrollment->id}");
    }

    public function test_guest_cannot_access_paid_lesson(): void
    {
        [$course, $lesson] = $this->createPaidCourseWithLesson();

        $this->get("/courses/{$course->slug}/lessons/{$lesson->slug}")
            ->assertRedirect('/login');
    }

    public function test_guest_can_access_free_published_lesson(): void
    {
        $instructor = User::factory()->create(['is_admin' => true]);
        $course = Course::create([
            'title' => 'Free Course',
            'slug' => 'free-course',
            'description' => 'Free course description',
            'content' => 'Free course content',
            'instructor_id' => $instructor->id,
            'status' => 'published',
            'difficulty_level' => 'beginner',
            'duration_minutes' => 60,
            'price' => 0,
        ]);

        $lesson = Lesson::create([
            'title' => 'Free Lesson',
            'slug' => 'free-lesson',
            'description' => 'Free lesson description',
            'content' => 'Free lesson body',
            'type' => 'video',
            'duration_minutes' => 20,
            'sort_order' => 1,
            'is_free' => true,
            'is_published' => true,
            'course_id' => $course->id,
        ]);

        $this->get("/courses/{$course->slug}/lessons/{$lesson->slug}")
            ->assertOk()
            ->assertSee('Free lesson body');
    }

    public function test_completed_paid_enrollment_can_still_access_paid_lesson(): void
    {
        [$course, $lesson, $learner] = $this->createPaidCourseWithLesson();

        Enrollment::create([
            'user_id' => $learner->id,
            'course_id' => $course->id,
            'status' => 'completed',
            'enrolled_at' => now()->subDays(5),
            'completed_at' => now()->subDay(),
            'progress_percentage' => 100,
            'payment_status' => 'paid',
            'paid_amount' => $course->price,
        ]);

        $this->actingAs($learner)
            ->get("/courses/{$course->slug}/lessons/{$lesson->slug}")
            ->assertOk()
            ->assertSee('Paid lesson body');
    }

    public function test_continue_learning_route_redirects_to_accessible_lesson(): void
    {
        [$course, $lesson, $learner] = $this->createPaidCourseWithLesson();

        $enrollment = Enrollment::create([
            'user_id' => $learner->id,
            'course_id' => $course->id,
            'status' => 'active',
            'enrolled_at' => now(),
            'progress_percentage' => 0,
            'payment_status' => 'paid',
            'paid_amount' => $course->price,
        ]);

        $this->actingAs($learner)
            ->get("/dashboard/enrollments/{$enrollment->id}/continue")
            ->assertRedirect("/courses/{$course->slug}/lessons/{$lesson->slug}");
    }

    private function createPaidCourseWithLesson(): array
    {
        $instructor = User::factory()->create(['is_admin' => true]);
        $learner = User::factory()->create(['is_admin' => false]);

        $course = Course::create([
            'title' => 'Paid Course',
            'slug' => 'paid-course',
            'description' => 'Paid course description',
            'content' => 'Paid course content',
            'instructor_id' => $instructor->id,
            'status' => 'published',
            'difficulty_level' => 'beginner',
            'duration_minutes' => 120,
            'price' => 150,
        ]);

        $lesson = Lesson::create([
            'title' => 'Paid Lesson',
            'slug' => 'paid-lesson',
            'description' => 'Paid lesson description',
            'content' => 'Paid lesson body',
            'type' => 'video',
            'duration_minutes' => 30,
            'sort_order' => 1,
            'is_free' => false,
            'is_published' => true,
            'course_id' => $course->id,
        ]);

        return [$course, $lesson, $learner];
    }
}
