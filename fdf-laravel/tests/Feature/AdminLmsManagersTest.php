<?php

namespace Tests\Feature;

use App\Livewire\Admin\CourseManager;
use App\Livewire\Admin\EnrollmentManager;
use App\Livewire\Admin\LessonManager;
use App\Livewire\Admin\LmsDashboard;
use App\Models\Category;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Lesson;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminLmsManagersTest extends TestCase
{
    use RefreshDatabase;

    public function test_course_manager_can_create_and_toggle_course_flags(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $category = Category::create([
            'name' => 'Professional Training',
            'slug' => 'professional-training',
            'description' => 'Course category for admin tests',
            'type' => 'course',
            'is_active' => true,
            'sort_order' => 1,
        ]);

        Livewire::actingAs($admin)
            ->test(CourseManager::class)
            ->set('title', 'Interpreter Foundations')
            ->set('description', 'Foundational interpreting course.')
            ->set('content', 'Course details and objectives.')
            ->set('category_id', $category->id)
            ->set('instructor_id', $admin->id)
            ->set('intro_video_url', 'https://example.com/intro/interpreter-foundations')
            ->set('difficulty_level', 'beginner')
            ->set('duration_minutes', 180)
            ->set('status', 'draft')
            ->set('max_students', 50)
            ->set('is_certificate_enabled', true)
            ->set('price', 99.99)
            ->set('is_featured', false)
            ->call('storeCourse')
            ->assertHasNoErrors();

        $course = Course::where('slug', 'interpreter-foundations')->firstOrFail();

        Livewire::actingAs($admin)
            ->test(CourseManager::class)
            ->call('toggleFeatured', $course->id)
            ->call('togglePublished', $course->id);

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'is_featured' => true,
            'status' => 'published',
        ]);
    }

    public function test_lesson_manager_can_create_and_reorder_lessons(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $course = Course::create([
            'title' => 'ASL Basics',
            'slug' => 'asl-basics',
            'description' => 'Introduction to ASL',
            'instructor_id' => $admin->id,
            'status' => 'published',
            'difficulty_level' => 'beginner',
            'duration_minutes' => 120,
            'price' => 0,
        ]);

        $lessonOne = Lesson::create([
            'title' => 'Alphabet',
            'slug' => 'alphabet',
            'content' => 'Alphabet lesson content',
            'course_id' => $course->id,
            'sort_order' => 1,
            'type' => 'video',
            'is_published' => true,
            'is_free' => true,
        ]);

        $lessonTwo = Lesson::create([
            'title' => 'Numbers',
            'slug' => 'numbers',
            'content' => 'Numbers lesson content',
            'course_id' => $course->id,
            'sort_order' => 2,
            'type' => 'video',
            'is_published' => false,
            'is_free' => false,
        ]);

        Livewire::actingAs($admin)
            ->test(LessonManager::class)
            ->set('title', 'Greetings')
            ->set('description', 'Greeting signs')
            ->set('content', 'Greetings lesson content')
            ->set('course_id', $course->id)
            ->set('video_url', 'https://example.com/videos/greetings')
            ->set('duration_minutes', 15)
            ->set('sort_order', 3)
            ->set('type', 'video')
            ->set('is_published', true)
            ->set('is_free', false)
            ->call('storeLesson')
            ->assertHasNoErrors();

        Livewire::actingAs($admin)
            ->test(LessonManager::class)
            ->call('moveUp', $lessonTwo->id)
            ->call('togglePublished', $lessonTwo->id)
            ->call('toggleFree', $lessonTwo->id);

        $this->assertDatabaseHas('lessons', [
            'title' => 'Greetings',
            'slug' => 'greetings',
            'course_id' => $course->id,
        ]);

        $this->assertDatabaseHas('lessons', [
            'id' => $lessonTwo->id,
            'sort_order' => 1,
            'is_published' => true,
            'is_free' => true,
        ]);

        $this->assertDatabaseHas('lessons', [
            'id' => $lessonOne->id,
            'sort_order' => 2,
        ]);
    }

    public function test_enrollment_manager_handles_create_completion_and_duplicate_guard(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $learner = User::factory()->create(['is_admin' => false]);
        $course = Course::create([
            'title' => 'Community Support 101',
            'slug' => 'community-support-101',
            'description' => 'Community support training',
            'instructor_id' => $admin->id,
            'status' => 'published',
            'difficulty_level' => 'intermediate',
            'duration_minutes' => 240,
            'price' => 120.00,
        ]);

        Livewire::actingAs($admin)
            ->test(EnrollmentManager::class)
            ->set('course_id', $course->id)
            ->set('user_id', $learner->id)
            ->set('enrolled_at', now()->subDay()->format('Y-m-d H:i:s'))
            ->set('status', 'active')
            ->set('progress_percentage', 0)
            ->set('payment_status', 'paid')
            ->set('paid_amount', 0)
            ->call('storeEnrollment')
            ->assertHasNoErrors();

        $enrollment = Enrollment::where('course_id', $course->id)
            ->where('user_id', $learner->id)
            ->firstOrFail();

        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'payment_status' => 'paid',
            'paid_amount' => 120.00,
            'status' => 'active',
        ]);

        Livewire::actingAs($admin)
            ->test(EnrollmentManager::class)
            ->call('updateProgress', $enrollment->id, 100);

        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'status' => 'completed',
            'progress_percentage' => 100,
        ]);

        Livewire::actingAs($admin)
            ->test(EnrollmentManager::class)
            ->set('course_id', $course->id)
            ->set('user_id', $learner->id)
            ->set('enrolled_at', now()->format('Y-m-d H:i:s'))
            ->set('status', 'active')
            ->set('progress_percentage', 0)
            ->set('payment_status', 'pending')
            ->set('paid_amount', 0)
            ->call('storeEnrollment')
            ->assertHasErrors(['duplicate']);

        $this->assertSame(1, Enrollment::count());
    }

    public function test_lms_dashboard_shows_aggregated_lms_stats(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $instructor = User::factory()->create(['is_admin' => true]);
        $learnerA = User::factory()->create(['is_admin' => false]);
        $learnerB = User::factory()->create(['is_admin' => false]);

        $courseA = Course::create([
            'title' => 'Advanced Interpreting',
            'slug' => 'advanced-interpreting',
            'description' => 'Advanced interpreting skills',
            'instructor_id' => $instructor->id,
            'status' => 'published',
            'difficulty_level' => 'advanced',
            'duration_minutes' => 300,
            'price' => 200,
            'enrollment_count' => 25,
            'rating' => 4.70,
        ]);

        $courseB = Course::create([
            'title' => 'Deaf Culture Essentials',
            'slug' => 'deaf-culture-essentials',
            'description' => 'Foundational culture course',
            'instructor_id' => $instructor->id,
            'status' => 'draft',
            'difficulty_level' => 'beginner',
            'duration_minutes' => 90,
            'price' => 0,
            'enrollment_count' => 10,
            'rating' => 4.20,
        ]);

        Lesson::create([
            'title' => 'Context and Ethics',
            'slug' => 'context-and-ethics',
            'content' => 'Lesson content',
            'course_id' => $courseA->id,
            'sort_order' => 1,
            'type' => 'video',
            'is_published' => true,
        ]);

        Lesson::create([
            'title' => 'Communication Models',
            'slug' => 'communication-models',
            'content' => 'Lesson content',
            'course_id' => $courseA->id,
            'sort_order' => 2,
            'type' => 'text',
            'is_published' => true,
        ]);

        Lesson::create([
            'title' => 'History and Community',
            'slug' => 'history-and-community',
            'content' => 'Lesson content',
            'course_id' => $courseB->id,
            'sort_order' => 1,
            'type' => 'video',
            'is_published' => false,
        ]);

        Enrollment::create([
            'course_id' => $courseA->id,
            'user_id' => $learnerA->id,
            'status' => 'active',
            'enrolled_at' => now()->subDays(2),
            'progress_percentage' => 40,
            'payment_status' => 'paid',
            'paid_amount' => 200,
        ]);

        Enrollment::create([
            'course_id' => $courseA->id,
            'user_id' => $learnerB->id,
            'status' => 'completed',
            'enrolled_at' => now()->subDays(5),
            'completed_at' => now()->subDay(),
            'progress_percentage' => 100,
            'payment_status' => 'paid',
            'paid_amount' => 200,
        ]);

        $component = Livewire::actingAs($admin)->test(LmsDashboard::class);

        $component->assertSet('courseCount', 2);
        $component->assertSet('publishedCourseCount', 1);
        $component->assertSet('lessonCount', 3);
        $component->assertSet('enrollmentCount', 2);
        $component->assertSet('activeEnrollmentCount', 1);
        $component->assertSet('completedEnrollmentCount', 1);
        $component->assertSet('totalLearners', 2);

        $this->assertEquals(50.0, $component->get('completionRate'));
        $this->assertCount(2, $component->get('recentEnrollments'));
        $this->assertCount(2, $component->get('topCourses'));
        $this->assertSame($courseA->id, $component->get('topCourses')[0]->id);
    }
}
