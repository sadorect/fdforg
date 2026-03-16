<?php

namespace Tests\Feature;

use App\Models\Page;
use App\Models\VisitLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_requests_are_tracked_as_site_and_page_visits(): void
    {
        Page::create([
            'title' => 'Home',
            'slug' => 'home',
            'content' => 'Homepage content',
            'status' => 'published',
        ]);

        Page::create([
            'title' => 'About',
            'slug' => 'about',
            'content' => 'About content',
            'status' => 'published',
        ]);

        $this->get('/')->assertOk();
        $this->get('/about')->assertOk();

        $this->assertSame(1, VisitLog::site()->count());
        $this->assertSame(2, VisitLog::page()->count());
    }

    public function test_footer_displays_total_site_visits(): void
    {
        Page::create([
            'title' => 'Home',
            'slug' => 'home',
            'content' => 'Homepage content',
            'status' => 'published',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Site visits: 1');
    }
}
