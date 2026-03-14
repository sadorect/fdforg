<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Event;
use App\Models\Page;
use App\Models\SiteSetting;
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

        $this->get(route('courses.index'))
            ->assertOk()
            ->assertSee('Sign In to Enroll')
            ->assertSee('Create Account');
    }

    public function test_homepage_uses_dynamic_homepage_sections(): void
    {
        Page::create([
            'title' => 'Home',
            'slug' => 'home',
            'content' => 'Home content',
            'status' => 'published',
            'meta_image' => 'pages/home-hero.jpg',
            'sections' => [
                'landing' => [
                    'headline' => 'A future without communication barriers is possible.',
                    'subheadline' => 'Support that centers deaf communities and helps them thrive.',
                    'hero_image_alt' => 'Hero image alt text',
                ],
                'identity' => [
                    'mission_body' => 'Mission content for the homepage.',
                    'vision_body' => 'Vision content for the homepage.',
                    'approach_body' => 'Approach content for the homepage.',
                ],
                'services' => [
                    'items' => [
                        [
                            'eyebrow' => 'Education',
                            'title' => 'Dynamic service one',
                            'description' => 'Description one.',
                            'cta_label' => 'Learn more',
                            'cta_url' => '/learning',
                        ],
                        [
                            'eyebrow' => 'Community',
                            'title' => 'Dynamic service two',
                            'description' => 'Description two.',
                            'cta_label' => 'See events',
                            'cta_url' => '/events',
                        ],
                        [
                            'eyebrow' => 'Advocacy',
                            'title' => 'Dynamic service three',
                            'description' => 'Description three.',
                            'cta_label' => 'Contact us',
                            'cta_url' => '/contact',
                        ],
                    ],
                ],
            ],
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('A future without communication barriers is possible.')
            ->assertSee('Mission content for the homepage.')
            ->assertSee('Dynamic service one')
            ->assertSee('storage/pages/home-hero.jpg', false);
    }

    public function test_homepage_highlights_nonprofit_mission_vision_and_services(): void
    {
        Page::create([
            'title' => 'Home',
            'slug' => 'home',
            'content' => 'Bridging the communication gap and empowering the deaf community through education, advocacy, and support.',
            'status' => 'published',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Building a more accessible future for deaf communities.')
            ->assertSee('Mission')
            ->assertSee('Vision')
            ->assertSee('How We Serve')
            ->assertSee('Sign language learning and access support');
    }

    public function test_homepage_can_hide_admin_controlled_trust_layer(): void
    {
        Page::create([
            'title' => 'Home',
            'slug' => 'home',
            'content' => 'Home content',
            'status' => 'published',
            'sections' => [
                'trust' => [
                    'visible' => false,
                    'title' => 'Trust section should stay hidden',
                    'body' => 'This trust section body should not appear.',
                    'story_visible' => true,
                    'story_title' => 'Hidden story title',
                    'partners_visible' => true,
                    'partners' => [
                        ['name' => 'Hidden partner one'],
                        ['name' => 'Hidden partner two'],
                        ['name' => 'Hidden partner three'],
                        ['name' => 'Hidden partner four'],
                        ['name' => 'Hidden partner five'],
                        ['name' => 'Hidden partner six'],
                    ],
                ],
            ],
        ]);

        $this->get('/')
            ->assertOk()
            ->assertDontSee('Trust section should stay hidden')
            ->assertDontSee('This trust section body should not appear.')
            ->assertDontSee('Hidden story title')
            ->assertDontSee('Hidden partner one');
    }

    public function test_homepage_can_render_partner_logo_strip(): void
    {
        Page::create([
            'title' => 'Home',
            'slug' => 'home',
            'content' => 'Home content',
            'status' => 'published',
            'sections' => [
                'trust' => [
                    'visible' => true,
                    'partners_visible' => true,
                    'partners_title' => 'Partners and supporters',
                    'partners' => [
                        [
                            'name' => 'Partner One',
                            'website_url' => 'https://partner-one.example',
                            'logo_path' => 'pages/partners/partner-one.png',
                        ],
                    ],
                ],
            ],
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('Partners and supporters')
            ->assertSee('storage/pages/partners/partner-one.png', false)
            ->assertSee('https://partner-one.example', false)
            ->assertSee('Partner One');
    }

    public function test_about_page_uses_dynamic_about_sections(): void
    {
        Page::create([
            'title' => 'About',
            'slug' => 'about',
            'content' => '<p>Detailed story about the organization.</p>',
            'status' => 'published',
            'meta_image' => 'pages/about-hero.jpg',
            'sections' => [
                'hero' => [
                    'headline' => 'A stronger About page can build trust.',
                    'subheadline' => 'Dynamic About content should carry the organization story clearly.',
                    'image_alt' => 'About page image alt text',
                ],
                'story' => [
                    'title' => 'Why the foundation exists',
                    'highlight' => 'A highlight statement about the mission.',
                ],
                'identity' => [
                    'mission_body' => 'Mission body for the About page.',
                    'vision_body' => 'Vision body for the About page.',
                    'values_body' => 'Values body for the About page.',
                ],
                'commitments' => [
                    'items' => [
                        [
                            'title' => 'Commitment one',
                            'description' => 'Description one.',
                        ],
                        [
                            'title' => 'Commitment two',
                            'description' => 'Description two.',
                        ],
                        [
                            'title' => 'Commitment three',
                            'description' => 'Description three.',
                        ],
                    ],
                ],
            ],
        ]);

        $this->get('/about')
            ->assertOk()
            ->assertSee('A stronger About page can build trust.')
            ->assertSee('Why the foundation exists')
            ->assertSee('Mission body for the About page.')
            ->assertSee('Commitment one')
            ->assertSee('storage/pages/about-hero.jpg', false);
    }

    public function test_programs_page_uses_dynamic_programs_sections(): void
    {
        $instructor = User::factory()->create(['is_admin' => true]);

        Course::create([
            'title' => 'Program Course',
            'slug' => 'program-course',
            'description' => 'Course description',
            'instructor_id' => $instructor->id,
            'status' => 'published',
            'difficulty_level' => 'beginner',
            'duration_minutes' => 90,
            'price' => 0,
        ]);

        Event::create([
            'title' => 'Community Event',
            'slug' => 'community-event',
            'description' => 'Event description',
            'excerpt' => 'Event excerpt',
            'start_date' => now()->addWeek(),
            'end_date' => now()->addWeek()->addHours(2),
            'location' => 'Lagos',
            'status' => 'upcoming',
            'registration_required' => false,
            'is_virtual' => false,
        ]);

        Page::create([
            'title' => 'Programs',
            'slug' => 'programs',
            'content' => '<p>Detailed programs story.</p>',
            'status' => 'published',
            'meta_image' => 'pages/programs-hero.jpg',
            'sections' => [
                'hero' => [
                    'headline' => 'Programs page headline',
                    'subheadline' => 'Programs page subheadline',
                    'image_alt' => 'Programs hero image alt',
                ],
                'story' => [
                    'title' => 'Programs story title',
                    'highlight' => 'Programs story highlight',
                ],
                'pillars' => [
                    'items' => [
                        [
                            'eyebrow' => 'Education',
                            'title' => 'Program pillar one',
                            'description' => 'Description one',
                            'cta_label' => 'Explore courses',
                            'cta_url' => '/learning',
                        ],
                        [
                            'eyebrow' => 'Community',
                            'title' => 'Program pillar two',
                            'description' => 'Description two',
                            'cta_label' => 'Contact us',
                            'cta_url' => '/contact',
                        ],
                        [
                            'eyebrow' => 'Advocacy',
                            'title' => 'Program pillar three',
                            'description' => 'Description three',
                            'cta_label' => 'Partner with us',
                            'cta_url' => '/contact',
                        ],
                        [
                            'eyebrow' => 'Opportunity',
                            'title' => 'Program pillar four',
                            'description' => 'Description four',
                            'cta_label' => 'See events',
                            'cta_url' => '/events',
                        ],
                    ],
                ],
                'audiences' => [
                    'items' => [
                        ['title' => 'Audience one', 'description' => 'Audience one description'],
                        ['title' => 'Audience two', 'description' => 'Audience two description'],
                        ['title' => 'Audience three', 'description' => 'Audience three description'],
                        ['title' => 'Audience four', 'description' => 'Audience four description'],
                    ],
                ],
                'outcomes' => [
                    'title' => 'Programs outcomes title',
                    'quote' => 'Programs outcomes quote',
                ],
            ],
        ]);

        $this->get(route('programs'))
            ->assertOk()
            ->assertSee('Programs page headline')
            ->assertSee('Programs story title')
            ->assertSee('Program pillar one')
            ->assertSee('Audience one')
            ->assertSee('Programs outcomes title')
            ->assertSee('Program Course')
            ->assertSee('Community Event')
            ->assertSee('storage/pages/programs-hero.jpg', false);
    }

    public function test_donations_page_uses_dynamic_donations_sections(): void
    {
        Page::create([
            'title' => 'Donations',
            'slug' => 'donations',
            'content' => '<p>Detailed donation appeal.</p>',
            'status' => 'published',
            'meta_image' => 'pages/donations-hero.jpg',
            'sections' => [
                'hero' => [
                    'headline' => 'Support the mission headline',
                    'subheadline' => 'Support the mission subheadline',
                    'image_alt' => 'Donations hero image alt',
                ],
                'story' => [
                    'title' => 'Why your support matters',
                    'highlight' => 'Support highlight statement',
                ],
                'bank' => [
                    'title' => 'Official transfer details',
                    'accounts' => [
                        [
                            'currency_label' => 'Naira (NGN)',
                            'account_name' => 'Friends of the Deaf Foundation',
                            'bank_name' => 'Community Bank NGN',
                            'account_number' => '0123456789',
                            'routing_code' => '',
                            'note' => 'Use this account for local transfers.',
                        ],
                        [
                            'currency_label' => 'US Dollar (USD)',
                            'account_name' => 'Friends of the Deaf Foundation',
                            'bank_name' => 'Community Bank USD',
                            'account_number' => 'USD-000123456',
                            'routing_code' => 'SWIFT-USD-001',
                            'note' => 'Use this account for dollar transfers.',
                        ],
                        [
                            'currency_label' => 'Euro (EUR)',
                            'account_name' => 'Friends of the Deaf Foundation',
                            'bank_name' => 'Community Bank EUR',
                            'account_number' => 'EUR-000123456',
                            'routing_code' => 'IBAN-EUR-001',
                            'note' => 'Use this account for euro transfers.',
                        ],
                    ],
                ],
                'acknowledgement' => [
                    'title' => 'Notify us after donating',
                    'email_address' => 'donations@example.org',
                    'sms_number' => '+1234567890',
                ],
                'impact' => [
                    'title' => 'What support makes possible',
                    'items' => [
                        [
                            'amount' => 'Community Support',
                            'title' => 'Impact card one',
                            'description' => 'Impact description one.',
                        ],
                        [
                            'amount' => 'Program Delivery',
                            'title' => 'Impact card two',
                            'description' => 'Impact description two.',
                        ],
                        [
                            'amount' => 'Long-Term Impact',
                            'title' => 'Impact card three',
                            'description' => 'Impact description three.',
                        ],
                    ],
                ],
            ],
        ]);

        $this->get(route('donations'))
            ->assertOk()
            ->assertSee('Support the mission headline')
            ->assertSee('Why your support matters')
            ->assertSee('Official transfer details')
            ->assertSee('0123456789')
            ->assertSee('USD-000123456')
            ->assertSee('Naira (NGN)')
            ->assertSee('US Dollar (USD)')
            ->assertSee('donations@example.org')
            ->assertSee('+1234567890')
            ->assertSee('Impact card one')
            ->assertSee('storage/pages/donations-hero.jpg', false);
    }

    public function test_contact_page_uses_dynamic_contact_sections_and_site_settings(): void
    {
        SiteSetting::setValue('footer_email', 'hello@example.org');
        SiteSetting::setValue('footer_phone', '+1234567890');
        SiteSetting::setValue('footer_address', "123 Support Lane\nLagos");

        Page::create([
            'title' => 'Contact',
            'slug' => 'contact',
            'content' => '<p>Detailed contact introduction.</p>',
            'status' => 'published',
            'meta_image' => 'pages/contact-hero.jpg',
            'sections' => [
                'hero' => [
                    'headline' => 'Reach our team quickly',
                    'subheadline' => 'Contact subheadline text',
                    'image_alt' => 'Contact hero image alt',
                ],
                'intro' => [
                    'title' => 'Why people contact us',
                    'highlight' => 'Contact highlight statement',
                ],
                'pathways' => [
                    'items' => [
                        [
                            'title' => 'General support',
                            'description' => 'Support description',
                            'cta_label' => 'Use the form',
                            'cta_url' => '#contact-form',
                        ],
                        [
                            'title' => 'Programs and events',
                            'description' => 'Programs description',
                            'cta_label' => 'Explore programs',
                            'cta_url' => '/programs-and-activities',
                        ],
                        [
                            'title' => 'Donations and partnerships',
                            'description' => 'Donations description',
                            'cta_label' => 'Support the mission',
                            'cta_url' => '/donations',
                        ],
                    ],
                ],
                'contact_info' => [
                    'title' => 'Use the channel that works best for you',
                ],
                'form' => [
                    'title' => 'Send us a message',
                ],
            ],
        ]);

        $this->get(route('contact'))
            ->assertOk()
            ->assertSee('Reach our team quickly')
            ->assertSee('Why people contact us')
            ->assertSee('General support')
            ->assertSee('Use the channel that works best for you')
            ->assertSee('Send us a message')
            ->assertSee('hello@example.org')
            ->assertSee('+1234567890')
            ->assertSee('123 Support Lane')
            ->assertSee('storage/pages/contact-hero.jpg', false);
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
            ->assertSee('data-nav-item="events" data-nav-active="true"', false);
    }

    public function test_public_layout_renders_back_to_top_button(): void
    {
        Page::create([
            'title' => 'Home',
            'slug' => 'home',
            'content' => 'Home content',
            'status' => 'published',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee('id="back-to-top"', false)
            ->assertSee('aria-label="Back to top"', false);
    }
}
