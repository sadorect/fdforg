<?php

namespace Tests\Feature;

use App\Models\VisitLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAnalyticsExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_export_filtered_analytics_to_pdf(): void
    {
        $admin = $this->createAdminUser();

        VisitLog::create([
            'visit_type' => 'site',
            'path' => '/blog/post-1',
            'route_name' => 'blog.show',
            'full_url' => 'http://localhost/blog/post-1',
            'session_id' => 'session-a',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'referrer' => '',
            'device_type' => 'desktop',
            'browser' => 'Chrome',
            'is_authenticated' => false,
            'visited_at' => now()->subDay(),
        ]);

        VisitLog::create([
            'visit_type' => 'page',
            'path' => '/blog/post-1',
            'route_name' => 'blog.show',
            'full_url' => 'http://localhost/blog/post-1',
            'session_id' => 'session-a',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'referrer' => '',
            'device_type' => 'desktop',
            'browser' => 'Chrome',
            'is_authenticated' => false,
            'visited_at' => now()->subDay(),
        ]);

        $response = $this->actingAs($admin)
            ->get('/admin/analytics/export/pdf?rangeDays=30&pathFilter=%2Fblog');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('attachment; filename="analytics-report-', $response->headers->get('content-disposition', ''));
    }
}
