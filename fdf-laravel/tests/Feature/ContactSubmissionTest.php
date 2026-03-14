<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_contact_captcha_can_refresh_without_reloading_page(): void
    {
        $response = $this->get(route('contact.captcha'));

        $response->assertOk();
        $response->assertJsonStructure(['question']);
        $response->assertSessionHas('contact_captcha_question');
        $response->assertSessionHas('contact_captcha_answer');

        $cacheControl = (string) $response->headers->get('Cache-Control');
        $question = $response->json('question');

        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertIsString($question);
        $this->assertMatchesRegularExpression('/^\d+ \+ \d+$/', $question);
    }

    public function test_contact_submission_succeeds_with_valid_math_captcha(): void
    {
        $response = $this->withSession([
            'contact_captcha_question' => '3 + 4',
            'contact_captcha_answer' => 7,
        ])->post('/contact', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Hello from a feature test.',
            'captcha_answer' => 7,
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('success');
    }

    public function test_contact_submission_fails_with_invalid_math_captcha(): void
    {
        $response = $this->from('/contact')
            ->withSession([
                'contact_captcha_question' => '3 + 4',
                'contact_captcha_answer' => 7,
            ])->post('/contact', [
                'name' => 'Test User',
                'email' => 'test@example.com',
                'message' => 'Hello from a feature test.',
                'captcha_answer' => 9,
            ]);

        $response->assertRedirect('/contact');
        $response->assertSessionHasErrors('captcha_answer');
    }
}
