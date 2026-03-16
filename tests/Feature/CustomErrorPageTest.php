<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomErrorPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_unknown_route_shows_custom_404_page(): void
    {
        $response = $this->get('/this-route-does-not-exist');

        $response->assertNotFound();
        $response->assertSee('Page Not Found');
        $response->assertSee('Go to Homepage');
    }
}
