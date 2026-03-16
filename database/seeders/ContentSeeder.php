<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Page;
use App\Models\Event;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing data
DB::statement('SET FOREIGN_KEY_CHECKS=0;');
DB::table('event_registrations')->delete();
        Page::truncate();
DB::table('events')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Importing scraped content...');
        
        // Import pages
        $this->importPages();
        
        // Import events
        $this->importEvents();
        
        $this->command->info('Content import completed!');
    }

    private function importPages(): void
    {
        $this->command->info('Importing pages...');
        
        // Define page mappings from scraped data
        $pageMappings = [
            'homepage' => [
                'title' => 'Friends of the Deaf Foundation',
                'slug' => 'home',
                'content' => '<p>Bridging the communication gap and empowering the deaf community through education, advocacy, and support.</p>',
                'meta_title' => 'Friends of the Deaf Foundation - Empowering the Deaf Community',
                'meta_description' => 'Friends of the Deaf Foundation is dedicated to bridging the communication gap and empowering the deaf community through education, advocacy, and support.',
                
            ],
            'about' => [
                'title' => 'About Us',
                'slug' => 'about',
                'content' => $this->getAboutContent(),
                'meta_title' => 'About Us - Friends of the Deaf Foundation',
                'meta_description' => 'Learn about Friends of the Deaf Foundation\'s mission, vision, and commitment to empowering the deaf community.',
                
                'metadata' => [
                    'show_cta' => true,
                    'cta_title' => 'Join Our Mission',
                    'cta_text' => 'Help us make a difference in the lives of deaf individuals and their families.',
                ],
            ],
            'contact' => [
                'title' => 'Contact Us',
                'slug' => 'contact',
                'content' => $this->getContactContent(),
                'meta_title' => 'Contact Us - Friends of the Deaf Foundation',
                'meta_description' => 'Get in touch with Friends of the Deaf Foundation. We\'re here to help and answer your questions.',
                
            ],
            'programs' => [
                'title' => 'Our Programs',
                'slug' => 'programs',
                'content' => $this->getProgramsContent(),
                'meta_title' => 'Our Programs - Friends of the Deaf Foundation',
                'meta_description' => 'Explore our comprehensive programs designed to support and empower the deaf community.',
                
                'metadata' => [
                    'show_cta' => true,
                ],
            ],
            'donations' => [
                'title' => 'Donate',
                'slug' => 'donations',
                'content' => $this->getDonationsContent(),
                'meta_title' => 'Donate - Friends of the Deaf Foundation',
                'meta_description' => 'Support our mission by making a donation to Friends of the Deaf Foundation.',
                
            ],
        ];

        foreach ($pageMappings as $pageData) {
            Page::create($pageData);
        }

        $this->command->info('Pages imported successfully!');
    }

    private function importEvents(): void
    {
        $this->command->info('Importing events...');
        
        // Sample events based on typical foundation activities
        $events = [
            [
                'title' => 'ASL Workshop for Beginners',
                'slug' => 'asl-workshop-beginners',
                'description' => '<p>Join us for an introductory American Sign Language workshop designed for beginners. Learn basic ASL vocabulary, grammar, and conversational skills in a supportive environment.</p><p>This workshop is perfect for family members, friends, and community members who want to learn how to communicate better with deaf individuals.</p>',
                'excerpt' => 'Learn basic American Sign Language in this beginner-friendly workshop.',
                'event_type' => 'workshop',
                'start_date' => now()->addDays(14),
                'end_date' => now()->addDays(14),
                'time' => '6:00 PM - 8:00 PM',
                'location' => 'Community Center, 123 Main St, Anytown, USA',
                'is_virtual' => false,
                'is_featured' => true,
                'status' => 'published',
                'max_attendees' => 30,
                'registration_url' => '/events/registration/asl-workshop-beginners',
            ],
            [
                'title' => 'Deaf Community Networking Event',
                'slug' => 'deaf-community-networking',
                'description' => '<p>Connect with fellow deaf and hard-of-hearing individuals, allies, and community members at our monthly networking event.</p><p>Enjoy refreshments, meet new people, and learn about local resources and opportunities for the deaf community.</p>',
                'excerpt' => 'Monthly networking event for the deaf community and allies.',
                'event_type' => 'social',
                'start_date' => now()->addDays(7),
                'end_date' => now()->addDays(7),
                'time' => '5:30 PM - 7:30 PM',
                'location' => 'Community Hall, 456 Oak Ave, Anytown, USA',
                'is_virtual' => false,
                'is_featured' => false,
                'status' => 'published',
                'max_attendees' => 50,
                'registration_url' => '/events/registration/deaf-networking',
            ],
            [
                'title' => 'Virtual ASL Practice Session',
                'slug' => 'virtual-asl-practice',
                'description' => '<p>Practice your ASL skills from the comfort of your home! Join our virtual practice sessions led by experienced ASL instructors.</p><p>All skill levels welcome. This is a great opportunity to practice conversational ASL in a supportive online environment.</p>',
                'excerpt' => 'Online ASL practice session for all skill levels.',
                'event_type' => 'workshop',
                'start_date' => now()->addDays(3),
                'end_date' => now()->addDays(3),
                'time' => '7:00 PM - 8:30 PM',
                'location' => 'Online via Zoom',
                'is_virtual' => true,
                'meeting_link' => 'https://zoom.us/j/example',
                'is_featured' => false,
                'status' => 'published',
                'max_attendees' => 25,
                'registration_url' => '/events/registration/virtual-asl-practice',
            ],
            [
                'title' => 'Deaf Awareness Month Celebration',
                'slug' => 'deaf-awareness-celebration',
                'description' => '<p>Join us as we celebrate Deaf Awareness Month with a special event featuring deaf artists, performers, and speakers.</p><p>This family-friendly event will showcase deaf culture, provide educational resources, and create opportunities for community connection.</p>',
                'excerpt' => 'Celebrate Deaf Awareness Month with performances and community activities.',
                'event_type' => 'celebration',
                'start_date' => now()->addMonths(1)->startOfMonth(),
                'end_date' => now()->addMonths(1)->startOfMonth(),
                'time' => '2:00 PM - 6:00 PM',
                'location' => 'City Park Pavilion, Anytown, USA',
                'is_virtual' => false,
                'is_featured' => true,
                'status' => 'published',
                'max_attendees' => 200,
                'registration_url' => '/events/registration/deaf-awareness-celebration',
            ],
        ];

        foreach ($events as $eventData) {
            Event::create($eventData);
        }

        $this->command->info('Events imported successfully!');
    }

    private function getAboutContent(): string
    {
        return '
<h2>Our Mission</h2>
<p>Friends of the Deaf Foundation is dedicated to bridging the communication gap and empowering the deaf community through education, advocacy, and support. We strive to create an inclusive society where deaf individuals have equal access to opportunities and resources.</p>

<h2>Our Vision</h2>
<p>We envision a world where deaf individuals are fully integrated into society, with barriers to communication eliminated and equal opportunities for education, employment, and social participation.</p>

<h2>What We Do</h2>
<ul>
<li>Provide educational programs and resources for deaf individuals and their families</li>
<li>Advocate for deaf rights and accessibility in all aspects of society</li>
<li>Offer support services and community building activities</li>
<li>Promote awareness and understanding of deaf culture and issues</li>
<li>Facilitate communication access through interpreters and assistive technology</li>
</ul>

<h2>Our Values</h2>
<ul>
<li><strong>Inclusion:</strong> We believe in full inclusion of deaf individuals in all aspects of society</li>
<li><strong>Empowerment:</strong> We empower deaf individuals to reach their full potential</li>
<li><strong>Education:</strong> We provide educational resources and opportunities for lifelong learning</li>
<li><strong>Advocacy:</strong> We advocate for the rights and needs of the deaf community</li>
<li><strong>Community:</strong> We build strong, supportive communities for deaf individuals</li>
</ul>
        ';
    }

    private function getContactContent(): string
    {
        return '
<h2>Get in Touch</h2>
<p>We\'re here to help! Whether you\'re a deaf individual seeking support, a family member looking for resources, or someone interested in learning more about our programs, we\'d love to hear from you.</p>

<h3>Contact Information</h3>
<ul>
<li><strong>Email:</strong> info@friendsofthedeaffoundation.org</li>
<li><strong>Phone:</strong> (555) 123-4567</li>
<li><strong>Video Phone:</strong> (555) 123-4568</li>
<li><strong>Address:</strong> 123 Foundation Street, Anytown, USA 12345</li>
</ul>

<h3>Office Hours</h3>
<p>Monday - Friday: 9:00 AM - 5:00 PM<br>
Saturday: 10:00 AM - 2:00 PM<br>
Sunday: Closed</p>

<h3>Get Directions</h3>
<p>Our office is conveniently located downtown with easy access to public transportation. We offer accessible parking and are fully wheelchair accessible.</p>

<h3>Interpreting Services</h3>
<p>ASL interpreters are available upon request for all appointments and events. Please let us know when scheduling if you need interpreting services.</p>
        ';
    }

    private function getProgramsContent(): string
    {
        return '
<h2>Our Programs</h2>
<p>Friends of the Deaf Foundation offers a comprehensive range of programs designed to support and empower the deaf community.</p>

<h3>Education Programs</h3>
<ul>
<li><strong>ASL Classes:</strong> Learn American Sign Language from beginner to advanced levels</li>
<li><strong>Deaf Education Support:</strong> Tutoring and educational support for deaf students</li>
<li><strong>Parent Education:</strong> Resources and training for parents of deaf children</li>
<li><strong>Literacy Programs:</strong> Reading and writing support for deaf individuals</li>
</ul>

<h3>Support Services</h3>
<ul>
<li><strong>Counseling Services:</strong> Mental health support from deaf-aware counselors</li>
<li><strong>Peer Support Groups:</strong> Regular meetings for deaf individuals and families</li>
<li><strong>Case Management:</strong> Assistance with accessing community resources</li>
<li><strong>Employment Services:</strong> Job training and placement assistance</li>
</ul>

<h3>Community Programs</h3>
<ul>
<li><strong>Social Events:</strong> Regular community gatherings and activities</li>
<li><strong>Cultural Events:</strong> Celebrations of deaf culture and heritage</li>
<li><strong>Advocacy Workshops:</strong> Training in self-advocacy and rights</li>
<li><strong>Family Events:</strong> Activities for deaf individuals and their families</li>
</ul>

<h3>Technical Support</h3>
<ul>
<li><strong>Assistive Technology:</strong> Guidance on communication devices and technology</li>
<li><strong>Captioning Services:</strong> Access to captioned media and events</li>
<li><strong>Video Relay Services:</strong> Support for using video relay technology</li>
</ul>
        ';
    }

    private function getDonationsContent(): string
    {
        return '
<h2>Support Our Mission</h2>
<p>Your generous donation helps us continue providing essential services and programs to the deaf community. Every contribution makes a difference in the lives of deaf individuals and their families.</p>

<h3>Ways to Give</h3>

<h4>Online Donation</h4>
<p>Make a secure online donation using our payment portal. We accept all major credit cards and bank transfers.</p>

<h4>Monthly Giving</h4>
<p>Become a monthly supporter and provide sustained funding for our programs. Monthly giving helps us plan ahead and ensure consistent service delivery.</p>

<h4>Legacy Giving</h4>
<p>Include Friends of the Deaf Foundation in your estate planning and leave a lasting legacy of support for the deaf community.</p>

<h3>What Your Donation Supports</h3>
<ul>
<li>ASL classes and educational programs</li>
<li>Counseling and support services</li>
<li>Community events and activities</li>
<li>Advocacy and outreach efforts</li>
<li>Assistive technology and equipment</li>
<li>Interpreter services for events</li>
</ul>

<h3>Corporate Sponsorship</h3>
<p>We welcome corporate partnerships and sponsorships. Contact us to learn about sponsorship opportunities and how your business can support the deaf community.</p>

<h3>Volunteer</h3>
<p>Consider donating your time and skills! We have various volunteer opportunities available for individuals and groups.</p>

<p><strong>Thank you for your support!</strong></p>
        ';
    }
}