<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\HeroSlide;
use App\Models\Page;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicNavigationAndHeroTest extends TestCase
{
    use RefreshDatabase;

    public function test_navigation_only_lists_published_page_links(): void
    {
        Page::create([
            'title' => 'Home',
            'slug' => 'home',
            'content' => 'Home content',
            'status' => 'published',
        ]);

        Page::create([
            'title' => 'About',
            'slug' => 'about',
            'content' => 'About content',
            'status' => 'published',
        ]);

        Page::create([
            'title' => 'Programs',
            'slug' => 'programs',
            'content' => 'Programs content',
            'status' => 'archived',
        ]);

        Page::create([
            'title' => 'Donations',
            'slug' => 'donations',
            'content' => 'Donations content',
            'status' => 'draft',
        ]);

        Page::create([
            'title' => 'Contact',
            'slug' => 'contact',
            'content' => 'Contact content',
            'status' => 'published',
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee(route('about'), false);
        $response->assertSee(route('contact'), false);
        $response->assertDontSee(route('programs'), false);
        $response->assertDontSee(route('donations'), false);
    }

    public function test_guest_auth_links_are_removed_from_nav_and_visible_on_courses_page(): void
    {
        Page::create([
            'title' => 'Home',
            'slug' => 'home',
            'content' => 'Home content',
            'status' => 'published',
        ]);

        $instructor = User::factory()->create(['is_admin' => true]);
        Course::create([
            'title' => 'Intro Course',
            'slug' => 'intro-course',
            'description' => 'Course description',
            'instructor_id' => $instructor->id,
            'status' => 'published',
            'difficulty_level' => 'beginner',
            'duration_minutes' => 90,
            'price' => 0,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Sign In')
            ->assertDontSee('Create Account');

        $this->get('/courses')
            ->assertOk()
            ->assertSee('Sign In to Enroll')
            ->assertSee('Create Account');
    }

    public function test_homepage_uses_active_hero_slides(): void
    {
        Page::create([
            'title' => 'Home',
            'slug' => 'home',
            'content' => 'Home content',
            'status' => 'published',
        ]);

        HeroSlide::create([
            'title' => 'Active Hero Slide',
            'subtitle' => 'Active subtitle',
            'content' => 'Active slide content',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        HeroSlide::create([
            'title' => 'Inactive Hero Slide',
            'subtitle' => 'Inactive subtitle',
            'content' => 'Inactive slide content',
            'sort_order' => 2,
            'is_active' => false,
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Active Hero Slide')
            ->assertDontSee('Inactive Hero Slide');
    }

    public function test_public_navigation_marks_active_menu_item(): void
    {
        Page::create([
            'title' => 'Home',
            'slug' => 'home',
            'content' => 'Home content',
            'status' => 'published',
        ]);

        $this->get('/events')
            ->assertOk()
            ->assertSee('transition bg-blue-100 text-blue-700">Events</a>', false);
    }
}
