<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserAuthCaptchaTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_has_math_captcha(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
        $response->assertSee('Math CAPTCHA');
        $response->assertSee('data-captcha-status', false);
        $this->assertNotNull(session('user_auth_captcha_question'));
        $this->assertNotNull(session('user_auth_captcha_answer'));
    }

    public function test_auth_captcha_can_refresh_without_reloading_page(): void
    {
        $response = $this->get(route('auth.captcha'));

        $response->assertOk();
        $response->assertJsonStructure(['question']);
        $response->assertSessionHas('user_auth_captcha_question');
        $response->assertSessionHas('user_auth_captcha_answer');

        $this->assertStringContainsString('no-store', (string) $response->headers->get('Cache-Control'));
    }

    public function test_login_fails_with_invalid_math_captcha(): void
    {
        $user = User::factory()->create([
            'email' => 'learner@example.com',
            'password' => 'password',
        ]);

        $response = $this
            ->withSession([
                'user_auth_captcha_question' => '3 + 4',
                'user_auth_captcha_answer' => 7,
            ])
            ->post('/login', [
                'email' => $user->email,
                'password' => 'password',
                'captcha_answer' => 9,
            ]);

        $response->assertSessionHasErrors('captcha_answer');
        $this->assertGuest();
    }

    public function test_register_fails_with_invalid_math_captcha(): void
    {
        $response = $this
            ->withSession([
                'user_auth_captcha_question' => '2 + 5',
                'user_auth_captcha_answer' => 7,
            ])
            ->post('/register', [
                'name' => 'New Learner',
                'email' => 'new-learner@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'captcha_answer' => 3,
            ]);

        $response->assertSessionHasErrors('captcha_answer');
        $this->assertDatabaseMissing('users', ['email' => 'new-learner@example.com']);
    }

    public function test_register_succeeds_with_valid_math_captcha(): void
    {
        $response = $this
            ->withSession([
                'user_auth_captcha_question' => '2 + 5',
                'user_auth_captcha_answer' => 7,
            ])
            ->post('/register', [
                'name' => 'New Learner',
                'email' => 'new-learner@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'captcha_answer' => 7,
            ]);

        $response->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'new-learner@example.com']);
    }
}
