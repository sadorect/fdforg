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

    public function test_public_events_index_and_detail_present_rebuilt_sections(): void
    {
        $featuredEvent = Event::create([
            'title' => 'Featured Inclusion Forum',
            'slug' => 'featured-inclusion-forum',
            'description' => '<h2>Community planning</h2><p>A featured event for the community.</p><p>With more detail in a second paragraph.</p>',
            'excerpt' => 'Featured forum excerpt.',
            'start_date' => now()->addDays(4),
            'time' => '10:00 AM',
            'location' => 'Lagos Civic Hall',
            'status' => 'featured',
            'registration_required' => true,
            'max_attendees' => 80,
            'is_virtual' => true,
            'meeting_link' => 'https://example.org/meeting',
        ]);

        $upcomingEvent = Event::create([
            'title' => 'Community Sign Language Meetup',
            'slug' => 'community-sign-language-meetup',
            'description' => 'Meetup description.',
            'excerpt' => 'Meetup excerpt.',
            'start_date' => now()->addDays(9),
            'time' => '1:00 PM',
            'location' => 'Training Hall',
            'status' => 'upcoming',
            'registration_required' => false,
        ]);

        $pastEvent = Event::create([
            'title' => 'Past Outreach Session',
            'slug' => 'past-outreach-session',
            'description' => 'Past event description.',
            'excerpt' => 'Past excerpt.',
            'start_date' => now()->subDays(3),
            'time' => '11:00 AM',
            'location' => 'Community Center',
            'status' => 'past',
            'registration_required' => false,
        ]);

        $this->get('/events')
            ->assertOk()
            ->assertSee('Community gatherings, trainings, and outreach opportunities that turn inclusion into shared experience.')
            ->assertSee('Featured Inclusion Forum')
            ->assertSee('Community Sign Language Meetup')
            ->assertSee('Recently completed')
            ->assertSee('Past Outreach Session');

        $this->get('/events/'.$featuredEvent->slug)
            ->assertOk()
            ->assertSee('Why this gathering matters')
            ->assertSee('Community planning')
            ->assertSee('Registration and attendance')
            ->assertSee('This event happens online')
            ->assertSee('Featured Inclusion Forum')
            ->assertSee('Community Sign Language Meetup')
            ->assertSee('https://example.org/meeting', false);
    }

    public function test_event_registration_captcha_can_refresh_without_reloading_page(): void
    {
        $event = Event::create([
            'title' => 'Captcha Refresh Session',
            'slug' => 'captcha-refresh-session',
            'description' => 'Captcha refresh description.',
            'start_date' => now()->addDays(2),
            'status' => 'upcoming',
            'registration_required' => true,
        ]);

        $response = $this->getJson('/events/'.$event->slug.'/captcha');

        $response
            ->assertOk()
            ->assertJsonStructure(['question']);

        $this->assertNotNull(session('event_registration_captcha_answer'));
    }

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

        $this->get('/events/'.$event->slug.'/register')
            ->assertOk()
            ->assertSee('Reserve your place for Community Accessibility Workshop')
            ->assertSee('Tell us who is attending')
            ->assertSee('data-captcha-status', false)
            ->assertSee('aria-live="polite"', false);

        $this->withSession([
            'event_registration_captcha_question' => '2 + 6',
            'event_registration_captcha_answer' => 8,
        ])->post('/events/'.$event->slug.'/register', [
            'name' => 'Adaeze Okafor',
            'email' => 'adaeze@example.com',
            'phone' => '+2348012345678',
            'notes' => 'Looking forward to attending.',
            'captcha_answer' => 8,
        ])->assertRedirect('/events/'.$event->slug);

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
        ])->post('/events/'.$event->slug.'/register', [
            'name' => 'First User',
            'email' => 'first@example.com',
            'captcha_answer' => 5,
        ])->assertRedirect('/events/'.$event->slug);

        $this->withSession([
            'event_registration_captcha_question' => '1 + 4',
            'event_registration_captcha_answer' => 5,
        ])->post('/events/'.$event->slug.'/register', [
            'name' => 'First User Duplicate',
            'email' => 'first@example.com',
            'captcha_answer' => 5,
        ])->assertSessionHasErrors('registration');

        $this->withSession([
            'event_registration_captcha_question' => '2 + 3',
            'event_registration_captcha_answer' => 5,
        ])->post('/events/'.$event->slug.'/register', [
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
        ])->post('/events/'.$event->slug.'/register', [
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
