<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'meta_title',
        'meta_description',
        'meta_image',
        'status',
        'show_media_sidebar',
        'order',
        'metadata',
        'navigation',
        'sections',
    ];

    protected $casts = [
        'metadata' => 'array',
        'navigation' => 'array',
        'sections' => 'array',
        'show_media_sidebar' => 'boolean',
    ];

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Scope to get only published pages.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope to get pages in order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order', 'asc')->orderBy('title', 'asc');
    }

    /**
     * Get the meta title or fall back to the page title.
     */
    public function getMetaTitleAttribute($value): string
    {
        return $value ?: $this->title;
    }

    /**
     * Get the excerpt or generate one from content.
     */
    public function getExcerptAttribute($value): string
    {
        if ($value) {
            return $value;
        }

        // Generate excerpt from content (strip HTML, limit to 150 characters)
        $content = strip_tags($this->content);

        return strlen($content) > 150 ? substr($content, 0, 147).'...' : $content;
    }

    public static function defaultHomeSections(): array
    {
        return [
            'landing' => [
                'eyebrow' => 'Friends of the Deaf Foundation',
                'headline' => 'Building a more accessible future for deaf communities.',
                'subheadline' => 'We exist to make learning, connection, and everyday participation more inclusive for deaf and hard-of-hearing children, adults, families, and allies.',
                'primary_cta_label' => 'Get Support',
                'primary_cta_url' => '/contact',
                'secondary_cta_label' => 'Support the Mission',
                'secondary_cta_url' => '/donations',
                'hero_image_alt' => 'Friends of the Deaf Foundation community members learning together.',
            ],
            'analytics' => [
                'eyebrow' => 'Impact Snapshot',
                'title' => 'A quick look at the momentum building across learning, access, and community action.',
                'intro' => 'Use these figures to show visitors the scale, consistency, and practical outcomes behind the work.',
                'cta_label' => 'Browse Courses',
                'cta_url' => '/learning',
                'cards' => [
                    [
                        'value' => '12+',
                        'label' => 'Learning pathways',
                        'description' => 'Structured courses that help learners, families, and allies grow communication confidence.',
                    ],
                    [
                        'value' => '350+',
                        'label' => 'Learners engaged',
                        'description' => 'People reached through practical education, coaching, and inclusive learning opportunities.',
                    ],
                    [
                        'value' => '48',
                        'label' => 'Community gatherings',
                        'description' => 'Events and outreach moments that make belonging visible and participation easier.',
                    ],
                    [
                        'value' => '24',
                        'label' => 'Partner organisations',
                        'description' => 'Trusted collaborators helping widen access, awareness, and long-term support systems.',
                    ],
                    [
                        'value' => '90%',
                        'label' => 'Returning participants',
                        'description' => 'A sign that programs feel useful, welcoming, and worth coming back to.',
                    ],
                    [
                        'value' => '7 days',
                        'label' => 'Average response time',
                        'description' => 'Timely follow-up that helps people move from interest to practical next steps quickly.',
                    ],
                ],
            ],
            'testimonials' => [
                'eyebrow' => 'Community Voices',
                'title' => 'Stories from learners, families, and partners who have experienced the work up close.',
                'intro' => 'This strip should feel conversational and lived-in, giving visitors proof that support is practical and personal.',
                'items' => [
                    [
                        'quote' => 'The learning sessions gave our family a clearer way to communicate at home. We felt supported from the very first class.',
                        'name' => 'Parent participant',
                        'role' => 'Family learning program',
                    ],
                    [
                        'quote' => 'I joined for the course content and stayed for the sense of community. It feels like a place where access is taken seriously.',
                        'name' => 'Adult learner',
                        'role' => 'Community education cohort',
                    ],
                    [
                        'quote' => 'Their team made it easier for our organisation to think more carefully about inclusion and communication access.',
                        'name' => 'Programme partner',
                        'role' => 'Accessibility collaboration',
                    ],
                ],
            ],
            'identity' => [
                'mission_title' => 'Mission',
                'mission_body' => 'We bridge communication gaps and expand opportunity for deaf and hard-of-hearing people through education, advocacy, direct services, and community support.',
                'vision_title' => 'Vision',
                'vision_body' => 'We envision a society where deaf people can learn, lead, access services, and participate fully without communication barriers.',
                'approach_title' => 'Our Approach',
                'approach_body' => 'We combine direct support, inclusive learning, community-centered events, and practical partnerships that strengthen dignity, confidence, and belonging.',
            ],
            'services' => [
                'eyebrow' => 'How We Serve',
                'title' => 'Programs and services designed around access, confidence, and belonging.',
                'intro' => 'Our work is designed to meet people where they are: in classrooms, families, communities, and public spaces where inclusion must become real.',
                'items' => [
                    [
                        'eyebrow' => 'Education',
                        'title' => 'Sign language learning and access support',
                        'description' => 'Learning paths, practical lessons, and accessible guidance that help deaf people, families, and allies communicate with confidence.',
                        'cta_label' => 'Explore learning',
                        'cta_url' => '/learning',
                    ],
                    [
                        'eyebrow' => 'Community',
                        'title' => 'Events, outreach, and safe spaces to belong',
                        'description' => 'Workshops, gatherings, and public programs that strengthen community ties and create more inclusive everyday experiences.',
                        'cta_label' => 'See events',
                        'cta_url' => '/events',
                    ],
                    [
                        'eyebrow' => 'Advocacy',
                        'title' => 'Family support, awareness, and partnerships',
                        'description' => 'Collaboration with supporters, caregivers, and partners to push for understanding, access, and long-term opportunity.',
                        'cta_label' => 'Connect with us',
                        'cta_url' => '/contact',
                    ],
                ],
            ],
            'impact' => [
                'eyebrow' => 'Impact',
                'title' => 'Change becomes real when support is practical, visible, and consistent.',
                'body' => 'From learning opportunities to public outreach, we focus on support people can feel in daily life: stronger communication, safer participation, and wider access to opportunity.',
                'quote' => 'Access changes everything. When deaf people are included from the start, families, schools, and communities all become stronger.',
                'quote_author' => 'Friends of the Deaf Foundation',
                'quote_role' => 'Community Commitment',
            ],
            'trust' => [
                'visible' => true,
                'eyebrow' => 'Trust Layer',
                'title' => 'Credibility grows when people can see the mission in action.',
                'body' => 'This section helps visitors move from interest to confidence by showing a lived story and the kinds of partnerships that make the work sustainable.',
                'story_visible' => true,
                'story_eyebrow' => 'Community Story',
                'story_title' => 'Belonging begins when communication is no longer a barrier.',
                'story_body' => 'Families, learners, and community members need spaces where support feels practical, respectful, and immediate. A clear story here helps visitors understand the human outcome behind the mission.',
                'story_name' => 'Friends of the Deaf Foundation',
                'story_role' => 'Community Spotlight',
                'partners_visible' => false,
                'partners_title' => 'Partners and supporters',
                'partners' => [],
            ],
            'accessibility' => [
                'eyebrow' => 'Accessibility Commitment',
                'title' => 'Accessibility is not an add-on. It shapes how we serve.',
                'body' => 'We design our programs, learning experiences, and public communication with inclusion in mind so that deaf communities can participate with dignity and clarity.',
                'items' => [
                    [
                        'title' => 'Communication access',
                        'description' => 'We prioritize communication support that helps people participate clearly and confidently.',
                    ],
                    [
                        'title' => 'Inclusive learning',
                        'description' => 'Programs and resources are designed to be practical, accessible, and supportive of different entry points.',
                    ],
                    [
                        'title' => 'Community-safe participation',
                        'description' => 'We create spaces that are welcoming, respectful, and centered on belonging.',
                    ],
                    [
                        'title' => 'Digital accessibility',
                        'description' => 'We continue improving our online content so information is easier to find, understand, and use.',
                    ],
                ],
            ],
            'involvement' => [
                'eyebrow' => 'Get Involved',
                'title' => 'There is more than one way to stand with the community.',
                'intro' => 'Whether you need support, want to learn, or hope to contribute, there is a meaningful path for you here.',
                'items' => [
                    [
                        'title' => 'Get Support',
                        'description' => 'Reach out if you need information, direction, or a starting point for support.',
                        'cta_label' => 'Contact us',
                        'cta_url' => '/contact',
                    ],
                    [
                        'title' => 'Learn',
                        'description' => 'Build communication skills and grow through accessible public courses and resources.',
                        'cta_label' => 'View courses',
                        'cta_url' => '/learning',
                    ],
                    [
                        'title' => 'Volunteer',
                        'description' => 'Support outreach, events, and awareness efforts that strengthen community connection.',
                        'cta_label' => 'See opportunities',
                        'cta_url' => '/events',
                    ],
                    [
                        'title' => 'Donate or Partner',
                        'description' => 'Help us expand programs, services, and long-term access for deaf communities.',
                        'cta_label' => 'Support the mission',
                        'cta_url' => '/donations',
                    ],
                ],
            ],
            'closing_cta' => [
                'title' => 'Help us expand access, dignity, and opportunity.',
                'body' => 'Join the movement by learning with us, connecting with our team, or supporting programs that make communication access more possible every day.',
                'primary_label' => 'Support the Mission',
                'primary_url' => '/donations',
                'secondary_label' => 'Contact Our Team',
                'secondary_url' => '/contact',
            ],
        ];
    }

    public static function defaultAboutSections(): array
    {
        return [
            'hero' => [
                'eyebrow' => 'About Friends of the Deaf Foundation',
                'headline' => 'We work to widen access, dignity, and opportunity for deaf communities.',
                'subheadline' => 'Our work is rooted in the belief that communication access should never stand between a person and their ability to learn, belong, and participate fully in society.',
                'primary_cta_label' => 'Explore Our Programs',
                'primary_cta_url' => '/programs-and-activities',
                'secondary_cta_label' => 'Contact Our Team',
                'secondary_cta_url' => '/contact',
                'image_alt' => 'Friends of the Deaf Foundation community members gathered together.',
            ],
            'story' => [
                'eyebrow' => 'Our Story',
                'title' => 'Why this work exists and why it matters now.',
                'highlight' => 'Deaf people deserve more than awareness. They deserve practical support, communication access, and environments where dignity is built into everyday life.',
            ],
            'identity' => [
                'mission_title' => 'Mission',
                'mission_body' => 'We bridge communication gaps and expand opportunity for deaf and hard-of-hearing people through education, advocacy, direct services, and community support.',
                'vision_title' => 'Vision',
                'vision_body' => 'We envision a society where deaf people can learn, lead, access services, and participate fully without communication barriers.',
                'values_title' => 'Values',
                'values_body' => 'We lead with accessibility, respect, community partnership, and practical action that helps people feel seen, supported, and included.',
            ],
            'commitments' => [
                'eyebrow' => 'How We Work',
                'title' => 'The principles that shape our programs, partnerships, and public voice.',
                'intro' => 'Our approach centers lived experience, practical service, and relationships that build long-term trust with deaf people, families, and allies.',
                'items' => [
                    [
                        'title' => 'Community-led support',
                        'description' => 'We build with the community, not around it, so our work stays relevant, respectful, and grounded in real needs.',
                    ],
                    [
                        'title' => 'Practical access',
                        'description' => 'We focus on support people can use in daily life, from communication access to learning opportunities and guidance.',
                    ],
                    [
                        'title' => 'Long-term partnership',
                        'description' => 'We work with families, institutions, and supporters to create more durable systems of inclusion and opportunity.',
                    ],
                ],
            ],
            'quote' => [
                'eyebrow' => 'Our Commitment',
                'text' => 'We want every deaf child, adult, family, and ally who encounters our work to feel that access is possible, support is real, and belonging is worth building together.',
                'author' => 'Friends of the Deaf Foundation',
                'role' => 'Organizational Promise',
            ],
            'closing_cta' => [
                'title' => 'Partner with us in building a more accessible future.',
                'body' => 'Whether you are seeking support, exploring partnership, or looking for a meaningful way to contribute, we would love to hear from you.',
                'primary_label' => 'Explore Our Programs',
                'primary_url' => '/programs-and-activities',
                'secondary_label' => 'Contact Our Team',
                'secondary_url' => '/contact',
            ],
        ];
    }

    public static function defaultProgramsSections(): array
    {
        return [
            'hero' => [
                'eyebrow' => 'Programs and Services',
                'headline' => 'Practical programs that turn inclusion into everyday support.',
                'subheadline' => 'From sign language learning to family guidance and community outreach, our programs are built to strengthen communication, confidence, and belonging for deaf people and the people around them.',
                'primary_cta_label' => 'Contact Our Team',
                'primary_cta_url' => '/contact',
                'secondary_cta_label' => 'See Upcoming Events',
                'secondary_cta_url' => '/events',
                'image_alt' => 'Friends of the Deaf Foundation programs in action.',
            ],
            'story' => [
                'eyebrow' => 'Program Philosophy',
                'title' => 'Support works best when it is useful, welcoming, and easy to access.',
                'highlight' => 'We design programs around real communication needs, confidence-building, and community participation, so people can move from isolation or uncertainty into stronger connection and opportunity.',
            ],
            'pillars' => [
                'eyebrow' => 'Program Areas',
                'title' => 'Services built around learning, connection, advocacy, and everyday access.',
                'intro' => 'Our work spans several practical pathways so that deaf people, families, and supporters can find help that matches their situation and goals.',
                'items' => [
                    [
                        'eyebrow' => 'Education',
                        'title' => 'Sign language learning and communication support',
                        'description' => 'Programs that build language confidence, improve communication, and make everyday interaction more possible for deaf people, families, and allies.',
                        'cta_label' => 'Explore courses',
                        'cta_url' => '/learning',
                    ],
                    [
                        'eyebrow' => 'Community',
                        'title' => 'Family guidance and community belonging',
                        'description' => 'Supportive spaces, outreach, and guidance that help families and community members build stronger, more inclusive relationships.',
                        'cta_label' => 'Contact us',
                        'cta_url' => '/contact',
                    ],
                    [
                        'eyebrow' => 'Advocacy',
                        'title' => 'Awareness, inclusion, and access partnerships',
                        'description' => 'Programs that encourage awareness, improve accessibility conversations, and help institutions become more responsive to deaf people.',
                        'cta_label' => 'Partner with us',
                        'cta_url' => '/contact',
                    ],
                    [
                        'eyebrow' => 'Opportunity',
                        'title' => 'Skills, events, and participation pathways',
                        'description' => 'Workshops, public programs, and opportunities that help participants keep growing, connecting, and showing up with confidence.',
                        'cta_label' => 'See events',
                        'cta_url' => '/events',
                    ],
                ],
            ],
            'audiences' => [
                'eyebrow' => 'Who We Serve',
                'title' => 'Programs designed for different starting points into support and learning.',
                'intro' => 'People arrive with different needs, so our programs are shaped to support children, adults, families, and institutions in different but connected ways.',
                'items' => [
                    [
                        'title' => 'Deaf children and youth',
                        'description' => 'Support that helps young people grow with stronger communication, access, and confidence.',
                    ],
                    [
                        'title' => 'Adults and lifelong learners',
                        'description' => 'Learning pathways, practical resources, and programs that support participation across different life stages.',
                    ],
                    [
                        'title' => 'Families and caregivers',
                        'description' => 'Guidance and communication support that help homes become more connected and less isolating.',
                    ],
                    [
                        'title' => 'Schools, organizations, and allies',
                        'description' => 'Partnership-facing support that helps institutions and communities become more inclusive in practical ways.',
                    ],
                ],
            ],
            'outcomes' => [
                'eyebrow' => 'What This Creates',
                'title' => 'The goal is not just service delivery. It is stronger participation, confidence, and connection.',
                'body' => 'Every program is designed to move people closer to communication access, everyday dignity, and environments where deaf people can participate more fully and more safely.',
                'quote' => 'Programs matter when they help people feel less shut out and more able to participate in school, family life, community spaces, and opportunities ahead of them.',
                'quote_author' => 'Friends of the Deaf Foundation',
                'quote_role' => 'Program Commitment',
            ],
            'closing_cta' => [
                'title' => 'Find the right program path or talk with our team.',
                'body' => 'Whether you are looking for learning opportunities, family support, partnership, or a place to begin, we can help point you toward the next step.',
                'primary_label' => 'Contact Our Team',
                'primary_url' => '/contact',
                'secondary_label' => 'Explore Learning',
                'secondary_url' => '/learning',
            ],
        ];
    }

    public static function defaultDonationsSections(): array
    {
        return [
            'hero' => [
                'eyebrow' => 'Support the Mission',
                'headline' => 'Your support helps turn access into real daily opportunity.',
                'subheadline' => 'We currently receive donations by direct bank transfer. Every contribution helps us sustain learning, outreach, and practical support for deaf communities.',
                'primary_cta_label' => 'View Bank Details',
                'primary_cta_url' => '#bank-transfer',
                'secondary_cta_label' => 'How to Notify Us',
                'secondary_cta_url' => '#notify-us',
                'image_alt' => 'Supporters helping Friends of the Deaf Foundation expand community access.',
            ],
            'story' => [
                'eyebrow' => 'Why Your Support Matters',
                'title' => 'Giving strengthens the programs, relationships, and access work behind every story of inclusion.',
                'highlight' => 'Support helps us sustain practical services that improve communication access, strengthen belonging, and keep families and learners connected to real opportunities.',
            ],
            'bank' => [
                'eyebrow' => 'Give by Bank Transfer',
                'title' => 'Use the official account details below to support the work.',
                'body' => 'We are not processing online card payments at this time. Direct bank transfer is currently the official donation route. After sending your gift, please notify us so we can acknowledge it properly.',
                'reference_note' => 'If possible, include your name or a recognizable transfer reference.',
                'accounts' => [
                    [
                        'currency_label' => 'Naira (NGN)',
                        'account_name' => 'Friends of the Deaf Foundation',
                        'bank_name' => 'Add Naira bank name in admin',
                        'account_number' => 'Add Naira account number in admin',
                        'routing_code' => '',
                        'note' => 'Use this account for Naira transfers.',
                    ],
                    [
                        'currency_label' => 'US Dollar (USD)',
                        'account_name' => 'Friends of the Deaf Foundation',
                        'bank_name' => 'Add USD bank name in admin',
                        'account_number' => 'Add USD account number in admin',
                        'routing_code' => '',
                        'note' => 'Use this account for USD transfers.',
                    ],
                    [
                        'currency_label' => 'Euro (EUR)',
                        'account_name' => 'Friends of the Deaf Foundation',
                        'bank_name' => 'Add EUR bank name in admin',
                        'account_number' => 'Add EUR account number in admin',
                        'routing_code' => '',
                        'note' => 'Use this account for EUR transfers.',
                    ],
                ],
            ],
            'acknowledgement' => [
                'eyebrow' => 'Notify Us After Donating',
                'title' => 'Help us register and acknowledge your support.',
                'body' => 'Once you have sent a donation, notify the foundation by email or SMS with your name, transfer date, amount, and bank reference so we can thank you and record it.',
                'email_label' => 'Send an email',
                'email_address' => '',
                'email_subject' => 'Donation notification',
                'email_message' => "Hello, I have made a donation to Friends of the Deaf Foundation.\nName:\nAmount:\nDate:\nBank/Reference:\nPhone:",
                'sms_label' => 'Send an SMS',
                'sms_number' => '',
                'sms_message' => 'Hello, I just made a donation to Friends of the Deaf Foundation. My name is ',
                'tip' => 'Please include your full name, the amount, the date of transfer, and any bank reference if available.',
            ],
            'impact' => [
                'eyebrow' => 'What Support Makes Possible',
                'title' => 'Every gift helps sustain practical support, not just good intentions.',
                'intro' => 'Use this section to help supporters understand the kinds of outcomes their giving makes more possible.',
                'items' => [
                    [
                        'amount' => 'Community Support',
                        'title' => 'Communication access and learning support',
                        'description' => 'Donations help strengthen the tools, sessions, and guidance that make communication more possible for deaf people and families.',
                    ],
                    [
                        'amount' => 'Program Delivery',
                        'title' => 'Events, outreach, and belonging',
                        'description' => 'Support helps sustain community-facing activities that reduce isolation and create safer, more inclusive spaces to participate.',
                    ],
                    [
                        'amount' => 'Long-Term Impact',
                        'title' => 'Advocacy and partnership work',
                        'description' => 'Giving also helps extend awareness, collaboration, and the kind of long-term inclusion work that changes systems over time.',
                    ],
                ],
            ],
            'closing_cta' => [
                'title' => 'Thank you for standing with deaf communities.',
                'body' => 'Whether you give once, support regularly by transfer, or help us share the work with others, your support strengthens real access and real belonging.',
                'primary_label' => 'Contact Our Team',
                'primary_url' => '/contact',
                'secondary_label' => 'Explore Our Programs',
                'secondary_url' => '/programs-and-activities',
            ],
        ];
    }

    public static function defaultContactSections(): array
    {
        return [
            'hero' => [
                'eyebrow' => 'Contact Friends of the Deaf Foundation',
                'headline' => 'Reach the team behind the support, programs, and partnerships.',
                'subheadline' => 'Whether you need guidance, want to support the mission, or are exploring a partnership, we would love to hear from you.',
                'primary_cta_label' => 'Send a Message',
                'primary_cta_url' => '#contact-form',
                'secondary_cta_label' => 'See Contact Options',
                'secondary_cta_url' => '#contact-options',
                'image_alt' => 'Friends of the Deaf Foundation team connecting with the community.',
            ],
            'intro' => [
                'eyebrow' => 'Start the Conversation',
                'title' => 'We welcome questions, support requests, donor follow-up, and partnership interest.',
                'highlight' => 'This page should make it easy for people to find the right channel, understand what to expect, and feel confident that their message will reach a real team.',
            ],
            'pathways' => [
                'eyebrow' => 'How We Can Help',
                'title' => 'Different needs, one clear place to start.',
                'intro' => 'People reach out for many reasons, so we make space for support requests, program questions, donor notifications, and collaborative opportunities.',
                'items' => [
                    [
                        'title' => 'Support and general inquiries',
                        'description' => 'Use the contact form if you need information, guidance, or help finding the right next step.',
                        'cta_label' => 'Use the contact form',
                        'cta_url' => '#contact-form',
                    ],
                    [
                        'title' => 'Programs and events',
                        'description' => 'If you want to learn more about current opportunities, courses, or community events, start here.',
                        'cta_label' => 'Explore programs',
                        'cta_url' => '/programs-and-activities',
                    ],
                    [
                        'title' => 'Donations and partnerships',
                        'description' => 'Reach out if you want to notify us about a donation, explore sponsorship, or discuss a partnership.',
                        'cta_label' => 'Support the mission',
                        'cta_url' => '/donations',
                    ],
                ],
            ],
            'contact_info' => [
                'eyebrow' => 'Contact Options',
                'title' => 'Use the channel that works best for you.',
                'body' => 'Our main contact details are maintained centrally, so this page can stay accurate while still giving people context for when to use each option.',
                'email_title' => 'Email',
                'email_body' => 'Best for detailed questions, donor notifications, partnership outreach, and support follow-up.',
                'phone_title' => 'Phone or SMS',
                'phone_body' => 'Useful for quick follow-up, urgent coordination, or short donor notifications after a transfer.',
                'address_title' => 'Address',
                'address_body' => 'Use the official address below for visits, mail, or formal correspondence where needed.',
            ],
            'form' => [
                'eyebrow' => 'Send a Message',
                'title' => 'Tell us how we can help.',
                'intro' => 'Use the form below to reach the team directly. Share as much context as you can, and let us know if you have a preferred response channel.',
                'response_promise' => 'We aim to review messages carefully and route them to the right person as soon as possible.',
                'accessibility_note' => 'If you need a specific communication accommodation or would prefer email/SMS rather than a call, mention that in your message.',
            ],
            'closing_cta' => [
                'title' => 'You can also explore the work while you wait for a reply.',
                'body' => 'If you are still learning about the foundation, our Programs, Events, and Donations pages are good next stops after you send your message.',
                'primary_label' => 'Explore Programs',
                'primary_url' => '/programs-and-activities',
                'secondary_label' => 'See Events',
                'secondary_url' => '/events',
            ],
        ];
    }

    public static function mergeHomeSections(?array $sections): array
    {
        $sections = is_array($sections) ? $sections : [];
        $defaults = static::defaultHomeSections();
        $merged = array_replace_recursive($defaults, $sections);

        $analyticsCards = is_array(data_get($sections, 'analytics.cards')) ? data_get($sections, 'analytics.cards') : [];
        $normalizedAnalyticsCards = [];

        foreach ($defaults['analytics']['cards'] as $index => $defaultCard) {
            $normalizedAnalyticsCards[] = array_replace($defaultCard, is_array($analyticsCards[$index] ?? null) ? $analyticsCards[$index] : []);
        }

        $testimonialItems = is_array(data_get($sections, 'testimonials.items')) ? data_get($sections, 'testimonials.items') : null;
        $testimonialTemplate = [
            'quote' => '',
            'name' => '',
            'role' => '',
        ];

        $normalizedTestimonials = $testimonialItems !== null
            ? array_values(array_map(
                fn ($item) => array_replace($testimonialTemplate, is_array($item) ? $item : []),
                $testimonialItems
            ))
            : $defaults['testimonials']['items'];

        $merged['analytics']['cards'] = $normalizedAnalyticsCards;
        $merged['testimonials']['items'] = $normalizedTestimonials;

        return $merged;
    }

    public static function mergeAboutSections(?array $sections): array
    {
        return array_replace_recursive(static::defaultAboutSections(), is_array($sections) ? $sections : []);
    }

    public static function mergeProgramsSections(?array $sections): array
    {
        return array_replace_recursive(static::defaultProgramsSections(), is_array($sections) ? $sections : []);
    }

    public static function mergeDonationsSections(?array $sections): array
    {
        $sections = is_array($sections) ? $sections : [];
        $defaults = static::defaultDonationsSections();

        if (! isset($sections['bank']['accounts']) && isset($sections['bank']) && is_array($sections['bank'])) {
            $sections['bank']['accounts'] = [
                [
                    'currency_label' => 'Primary Account',
                    'account_name' => $sections['bank']['account_name'] ?? '',
                    'bank_name' => $sections['bank']['bank_name'] ?? '',
                    'account_number' => $sections['bank']['account_number'] ?? '',
                    'routing_code' => '',
                    'note' => $sections['bank']['currency_note'] ?? '',
                ],
            ];
        }

        $merged = array_replace_recursive($defaults, $sections);
        $accounts = is_array($merged['bank']['accounts'] ?? null) ? $merged['bank']['accounts'] : [];

        $normalizedAccounts = [];

        foreach ($defaults['bank']['accounts'] as $index => $defaultAccount) {
            $normalizedAccounts[] = array_replace($defaultAccount, is_array($accounts[$index] ?? null) ? $accounts[$index] : []);
        }

        $merged['bank']['accounts'] = $normalizedAccounts;

        return $merged;
    }

    public static function mergeContactSections(?array $sections): array
    {
        return array_replace_recursive(static::defaultContactSections(), is_array($sections) ? $sections : []);
    }
}
