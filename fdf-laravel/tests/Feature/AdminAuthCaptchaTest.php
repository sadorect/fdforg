<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminAuthCaptchaTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_login_page_shows_math_captcha(): void
    {
        $response = $this->get('/admin/login');

        $response->assertOk();
        $response->assertSee('Math CAPTCHA');
        $response->assertSee('data-admin-captcha-status', false);
        $this->assertNotNull(session('admin_captcha_question'));
        $this->assertNotNull(session('admin_captcha_answer'));
    }

    public function test_admin_captcha_can_refresh_without_reloading_page(): void
    {
        $response = $this->get(route('admin.captcha'));

        $response->assertOk();
        $response->assertJsonStructure(['question']);
        $response->assertSessionHas('admin_captcha_question');
        $response->assertSessionHas('admin_captcha_answer');

        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
    }

    public function test_admin_login_fails_with_invalid_math_captcha(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'password' => 'password',
        ]);

        $response = $this
            ->withSession([
                'admin_captcha_question' => '2 + 3',
                'admin_captcha_answer' => 5,
            ])
            ->post('/admin/login', [
                'email' => $admin->email,
                'password' => 'password',
                'captcha_answer' => 8,
            ]);

        $response->assertSessionHasErrors('captcha_answer');
    }

    public function test_admin_login_succeeds_with_valid_math_captcha(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
            'password' => 'password',
        ]);

        $response = $this
            ->withSession([
                'admin_captcha_question' => '2 + 3',
                'admin_captcha_answer' => 5,
            ])
            ->post('/admin/login', [
                'email' => $admin->email,
                'password' => 'password',
                'captcha_answer' => 5,
            ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($admin);
    }
}
