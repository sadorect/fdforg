<?php

namespace Tests\Feature;

use App\Livewire\Admin\EmailTemplateManager;
use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminEmailTemplateManagerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_access_email_template_page(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)
            ->get('/admin/email-templates')
            ->assertOk()
            ->assertSee('Email Templates');
    }

    public function test_admin_can_edit_email_template_content(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        EmailTemplate::syncDefaults();
        $template = EmailTemplate::query()->where('key', 'course_enrollment_confirmation')->firstOrFail();

        Livewire::actingAs($admin)
            ->test(EmailTemplateManager::class)
            ->call('edit', $template->id)
            ->set('subject', 'Updated Subject {{course_title}}')
            ->set('body', "Hello {{user_name}},\nUpdated body content.")
            ->set('is_active', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('email_templates', [
            'id' => $template->id,
            'subject' => 'Updated Subject {{course_title}}',
        ]);
    }
}
