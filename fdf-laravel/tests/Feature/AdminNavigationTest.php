<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_navigation_contains_all_primary_sections(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $response = $this->actingAs($admin)->get('/admin/dashboard');

        $response->assertOk();
        $response->assertSee('Dashboard');
        $response->assertSee('Analytics');
        $response->assertSee('Pages');
        $response->assertSee('Events');
        $response->assertSee('Blog');
        $response->assertSee('Categories');
        $response->assertSee('Hero Slides');
        $response->assertSee('Site Settings');
        $response->assertSee('Email Templates');
        $response->assertSee('LMS Dashboard');
        $response->assertSee('Courses');
        $response->assertSee('Lessons');
        $response->assertSee('Enrollments');
        $response->assertSee('Users');
        $response->assertSee('Roles & Permissions', false);
        $response->assertSee('Profile Settings');
    }
}
