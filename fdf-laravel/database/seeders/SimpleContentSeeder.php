<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Page;
use App\Models\Event;
use Illuminate\Support\Facades\DB;

class SimpleContentSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Clear existing data
        Page::truncate();
        Event::truncate();
        DB::table('event_registrations')->truncate();

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->command->info('Importing sample content...');
        
        // Create sample pages
        $pages = [
            [
                'title' => 'Friends of the Deaf Foundation',
                'slug' => 'home',
                'content' => '<p>Bridging the communication gap and empowering the deaf community through education, advocacy, and support.</p>',
                'meta_title' => 'Friends of the Deaf Foundation - Empowering the Deaf Community',
                'meta_description' => 'Friends of the Deaf Foundation is dedicated to bridging the communication gap and empowering the deaf community through education, advocacy, and support.',
                'status' => 'published',
            ],
            [
                'title' => 'About Us',
                'slug' => 'about',
                'content' => '<h2>Our Mission</h2><p>Friends of the Deaf Foundation is dedicated to bridging the communication gap and empowering the deaf community through education, advocacy, and support.</p>',
                'meta_title' => 'About Us - Friends of the Deaf Foundation',
                'meta_description' => 'Learn about Friends of the Deaf Foundation\'s mission, vision, and commitment to empowering the deaf community.',
                'status' => 'published',
            ],
            [
                'title' => 'Contact Us',
                'slug' => 'contact',
                'content' => '<h2>Get in Touch</h2><p>Email: info@friendsofthedeaffoundation.org<br>Phone: (555) 123-4567</p>',
                'meta_title' => 'Contact Us - Friends of the Deaf Foundation',
                'meta_description' => 'Get in touch with Friends of the Deaf Foundation.',
                'status' => 'published',
            ],
            [
                'title' => 'Programs and Activities',
                'slug' => 'programs',
                'content' => '<h2>Our Programs</h2><p>We offer various programs to support the deaf community including educational workshops, social events, and advocacy initiatives.</p>',
                'meta_title' => 'Programs and Activities - Friends of the Deaf Foundation',
                'meta_description' => 'Explore our programs and activities designed to empower and support the deaf community.',
                'status' => 'published',
            ],
            [
                'title' => 'Donations',
                'slug' => 'donations',
                'content' => '<h2>Support Our Mission</h2><p>Your generous donations help us continue our work in supporting the deaf community. Every contribution makes a difference.</p>',
                'meta_title' => 'Donate - Friends of the Deaf Foundation',
                'meta_description' => 'Support Friends of the Deaf Foundation with your donation to help us empower the deaf community.',
                'status' => 'published',
            ],
            [
                'title' => 'Accessibility',
                'slug' => 'accessibility',
                'content' => '<h2>Accessibility Commitment</h2><p>We are committed to ensuring our website and services are accessible to everyone, including those with hearing impairments.</p>',
                'meta_title' => 'Accessibility - Friends of the Deaf Foundation',
                'meta_description' => 'Learn about our commitment to accessibility and inclusive design for the deaf community.',
                'status' => 'published',
            ],
        ];

        foreach ($pages as $pageData) {
            Page::create($pageData);
        }

        // Create sample events
        $events = [
            [
                'title' => 'ASL Workshop for Beginners',
                'slug' => 'asl-workshop-beginners',
                'description' => 'Join us for an introductory American Sign Language workshop perfect for beginners who want to learn basic communication skills.',
                'start_date' => now()->addDays(7),
                'end_date' => now()->addDays(7),
                'time' => '10:00 AM',
                'location' => 'Community Center, Room 101',
                'price' => 'Free',
                'status' => 'upcoming',
            ],
            [
                'title' => 'Deaf Community Social Gathering',
                'slug' => 'deaf-community-social',
                'description' => 'A monthly social gathering for the deaf community to connect, share experiences, and build friendships.',
                'start_date' => now()->addDays(14),
                'end_date' => now()->addDays(14),
                'time' => '6:00 PM',
                'location' => 'Community Hall',
                'price' => 'Free',
                'status' => 'upcoming',
            ],
            [
                'title' => 'Virtual Accessibility Training',
                'slug' => 'virtual-accessibility-training',
                'description' => 'Learn about digital accessibility and how to make your content more accessible to deaf and hard-of-hearing individuals.',
                'start_date' => now()->addDays(21),
                'end_date' => now()->addDays(21),
                'time' => '2:00 PM',
                'location' => 'Online',
                'price' => '$25',
                'status' => 'upcoming',
                'is_virtual' => true,
                'meeting_link' => 'https://zoom.us/meeting123',
            ],
        ];

        foreach ($events as $eventData) {
            Event::create($eventData);
        }

        $this->command->info('Sample content imported successfully!');
    }
}