<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'name',
        'description',
        'subject',
        'body',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function defaults(): array
    {
        return [
            [
                'key' => 'course_enrollment_confirmation',
                'name' => 'Course Enrollment Confirmation',
                'description' => 'Sent when a user enrolls in a course.',
                'subject' => 'Enrollment Confirmed: {{course_title}}',
                'body' => implode("\n\n", [
                    'Hello {{user_name}},',
                    'You have successfully enrolled in "{{course_title}}".',
                    'Course fee: {{course_price}}.',
                    '{{payment_instructions}}',
                    'You can track progress from your dashboard: {{dashboard_url}}',
                    'Regards,',
                    'Friends of the Deaf Foundation',
                ]),
                'is_active' => true,
            ],
            [
                'key' => 'course_payment_instructions',
                'name' => 'Course Payment Instructions',
                'description' => 'Reusable payment instruction block for paid courses.',
                'subject' => 'Course Payment Instructions',
                'body' => implode("\n", [
                    'Payment Status: {{payment_status}}',
                    'If you are paying by bank transfer, send proof of payment with your full name and course title to {{support_email}}.',
                    'Amount due: {{course_price}}.',
                ]),
                'is_active' => true,
            ],
            [
                'key' => 'event_registration_confirmation',
                'name' => 'Event Registration Confirmation',
                'description' => 'Sent when a user registers for an event.',
                'subject' => 'Event Registration Confirmed: {{event_title}}',
                'body' => implode("\n\n", [
                    'Hello {{registrant_name}},',
                    'You are registered for "{{event_title}}" on {{event_date}} at {{event_time}}.',
                    'Location: {{event_location}}.',
                    'Regards,',
                    'Friends of the Deaf Foundation',
                ]),
                'is_active' => true,
            ],
            [
                'key' => 'user_registration_welcome',
                'name' => 'User Registration Welcome',
                'description' => 'Sent when a new user account is created.',
                'subject' => 'Welcome to Friends of the Deaf Foundation',
                'body' => implode("\n\n", [
                    'Hello {{user_name}},',
                    'Your account has been created successfully.',
                    'You can explore courses here: {{courses_url}}',
                    'Regards,',
                    'Friends of the Deaf Foundation',
                ]),
                'is_active' => true,
            ],
        ];
    }

    public static function syncDefaults(): void
    {
        foreach (self::defaults() as $template) {
            self::query()->firstOrCreate(
                ['key' => $template['key']],
                $template
            );
        }
    }
}
