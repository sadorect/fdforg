<?php

namespace Tests\Feature;

use App\Mail\TemplateNotificationMail;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PublicEventRegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_user_can_register_for_event_that_requires_registration(): void
    {
        Mail::fake();

        $event = Event::create([
            'title' => 'Community Accessibility Workshop',
            'slug' => 'community-accessibility-workshop',
            'description' => 'Workshop description.',
            'start_date' => now()->addDays(5),
            'time' => '11:00 AM',
            'location' => 'Lagos Community Center',
            'status' => 'upcoming',
            'registration_required' => true,
            'max_attendees' => 50,
        ]);

        $this->get('/events/' . $event->slug . '/register')
            ->assertOk()
            ->assertSee('Event Registration');

        $this->withSession([
            'event_registration_captcha_question' => '2 + 6',
            'event_registration_captcha_answer' => 8,
        ])->post('/events/' . $event->slug . '/register', [
            'name' => 'Adaeze Okafor',
            'email' => 'adaeze@example.com',
            'phone' => '+2348012345678',
            'notes' => 'Looking forward to attending.',
            'captcha_answer' => 8,
        ])->assertRedirect('/events/' . $event->slug);

        $this->assertDatabaseHas('event_registrations', [
            'event_id' => $event->id,
            'email' => 'adaeze@example.com',
            'status' => 'registered',
        ]);

        Mail::assertSent(TemplateNotificationMail::class, function (TemplateNotificationMail $mail) {
            return $mail->hasTo('adaeze@example.com')
                && str_contains($mail->body, 'Community Accessibility Workshop');
        });
    }

    public function test_event_registration_prevents_duplicates_and_respects_capacity(): void
    {
        $event = Event::create([
            'title' => 'Small Capacity Session',
            'slug' => 'small-capacity-session',
            'description' => 'Small event.',
            'start_date' => now()->addDays(3),
            'time' => '9:00 AM',
            'location' => 'Training Hall',
            'status' => 'upcoming',
            'registration_required' => true,
            'max_attendees' => 1,
        ]);

        $this->withSession([
            'event_registration_captcha_question' => '1 + 4',
            'event_registration_captcha_answer' => 5,
        ])->post('/events/' . $event->slug . '/register', [
            'name' => 'First User',
            'email' => 'first@example.com',
            'captcha_answer' => 5,
        ])->assertRedirect('/events/' . $event->slug);

        $this->withSession([
            'event_registration_captcha_question' => '1 + 4',
            'event_registration_captcha_answer' => 5,
        ])->post('/events/' . $event->slug . '/register', [
            'name' => 'First User Duplicate',
            'email' => 'first@example.com',
            'captcha_answer' => 5,
        ])->assertSessionHasErrors('registration');

        $this->withSession([
            'event_registration_captcha_question' => '2 + 3',
            'event_registration_captcha_answer' => 5,
        ])->post('/events/' . $event->slug . '/register', [
            'name' => 'Second User',
            'email' => 'second@example.com',
            'captcha_answer' => 5,
        ])->assertSessionHasErrors('registration');
    }

    public function test_event_registration_fails_with_invalid_captcha(): void
    {
        $event = Event::create([
            'title' => 'Captcha Protected Event',
            'slug' => 'captcha-protected-event',
            'description' => 'Captcha protected event.',
            'start_date' => now()->addDays(2),
            'status' => 'upcoming',
            'registration_required' => true,
        ]);

        $this->withSession([
            'event_registration_captcha_question' => '3 + 4',
            'event_registration_captcha_answer' => 7,
        ])->post('/events/' . $event->slug . '/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'captcha_answer' => 9,
        ])->assertSessionHasErrors('captcha_answer');

        $this->assertDatabaseMissing('event_registrations', [
            'event_id' => $event->id,
            'email' => 'test@example.com',
        ]);
    }
}
