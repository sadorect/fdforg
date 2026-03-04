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
        $response->assertSee('Admin Manual');
        $response->assertSee('Pages');
        $response->assertSee('Events');
        $response->assertSee('Gallery');
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
        $response->assertSeeText('Open Admin & LMS Manual', false);
    }

    public function test_admin_navigation_marks_active_submenu_item(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/admin/courses')
            ->assertOk()
            ->assertSee('href="' . route('admin.courses') . '" class="block px-4 py-2 text-sm bg-blue-50 font-semibold text-blue-700"', false);
    }
}
