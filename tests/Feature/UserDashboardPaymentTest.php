<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserDashboardPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_user_dashboard(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
        $this->get('/dashboard/payments')->assertRedirect('/login');
    }

    public function test_user_can_view_dashboard_and_pay_pending_enrollment(): void
    {
        $learner = User::factory()->create(['is_admin' => false]);
        $instructor = User::factory()->create(['is_admin' => true]);
        $course = Course::create([
            'title' => 'Paid Course',
            'slug' => 'paid-course',
            'description' => 'Paid course for payment flow',
            'instructor_id' => $instructor->id,
            'status' => 'published',
            'difficulty_level' => 'intermediate',
            'duration_minutes' => 120,
            'price' => 125.00,
        ]);

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
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Learner Dashboard')
            ->assertSee('Paid Course');

        $this->actingAs($learner)
            ->get('/dashboard/payments/'.$enrollment->id)
            ->assertOk()
            ->assertSee('Complete Payment')
            ->assertSee('data-captcha-status', false);

        $this->actingAs($learner)
            ->getJson(route('dashboard.pay.captcha', $enrollment->id))
            ->assertOk()
            ->assertJsonStructure(['question']);

        $this->actingAs($learner)
            ->withSession([
                'payment_action_captcha_question' => '2 + 5',
                'payment_action_captcha_answer' => 7,
            ])
            ->post('/dashboard/payments/'.$enrollment->id, [
                'card_name' => 'Test Learner',
                'card_number' => '4242424242424242',
                'expiry' => '12/30',
                'cvv' => '123',
                'captcha_answer' => 7,
            ])
            ->assertRedirect('/dashboard');

        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollment->id,
            'payment_status' => 'paid',
            'paid_amount' => 125.00,
        ]);
    }
}
