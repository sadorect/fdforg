<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class UserPasswordResetCaptchaTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_page_has_math_captcha(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertOk();
        $response->assertSee('Math CAPTCHA');
        $response->assertSee('data-captcha-status', false);
    }

    public function test_forgot_password_request_fails_with_invalid_captcha(): void
    {
        $user = User::factory()->create(['email' => 'reset-me@example.com']);

        $response = $this
            ->withSession([
                'user_auth_captcha_question' => '4 + 2',
                'user_auth_captcha_answer' => 6,
            ])
            ->post('/forgot-password', [
                'email' => $user->email,
                'captcha_answer' => 8,
            ]);

        $response->assertSessionHasErrors('captcha_answer');
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_forgot_password_request_creates_reset_token_with_valid_captcha(): void
    {
        $user = User::factory()->create(['email' => 'reset-ok@example.com']);

        $response = $this
            ->withSession([
                'user_auth_captcha_question' => '4 + 2',
                'user_auth_captcha_answer' => 6,
            ])
            ->post('/forgot-password', [
                'email' => $user->email,
                'captcha_answer' => 6,
            ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_password_reset_fails_with_invalid_captcha(): void
    {
        $user = User::factory()->create([
            'email' => 'invalid-reset@example.com',
            'password' => 'oldpassword',
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this
            ->withSession([
                'user_auth_captcha_question' => '1 + 1',
                'user_auth_captcha_answer' => 2,
            ])
            ->post('/reset-password', [
                'token' => $token,
                'email' => $user->email,
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
                'captcha_answer' => 5,
            ]);

        $response->assertSessionHasErrors('captcha_answer');
        $this->assertTrue(Hash::check('oldpassword', $user->fresh()->password));
    }

    public function test_password_reset_succeeds_with_valid_captcha(): void
    {
        $user = User::factory()->create([
            'email' => 'valid-reset@example.com',
            'password' => 'oldpassword',
        ]);

        $token = Password::broker()->createToken($user);

        $response = $this
            ->withSession([
                'user_auth_captcha_question' => '1 + 1',
                'user_auth_captcha_answer' => 2,
            ])
            ->post('/reset-password', [
                'token' => $token,
                'email' => $user->email,
                'password' => 'newpassword123',
                'password_confirmation' => 'newpassword123',
                'captcha_answer' => 2,
            ]);

        $response->assertRedirect('/login');
        $this->assertTrue(Hash::check('newpassword123', $user->fresh()->password));
    }
}
