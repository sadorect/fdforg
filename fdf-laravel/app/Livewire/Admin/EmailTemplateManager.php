<?php

namespace App\Livewire\Admin;

use App\Models\EmailTemplate;
use App\Support\AdminPermissions;

class EmailTemplateManager extends AdminComponent
{
    protected array $adminAbilities = [AdminPermissions::MANAGE_EMAIL_TEMPLATES];

    public $templates;

    public $selectedTemplateId = null;

    public $name = '';

    public $description = '';

    public $subject = '';

    public $body = '';

    public $is_active = true;

    protected $rules = [
        'subject' => 'required|string|max:255',
        'body' => 'required|string',
        'is_active' => 'boolean',
    ];

    public function mount(): void
    {
        EmailTemplate::syncDefaults();
        $this->loadTemplates();
    }

    public function loadTemplates(): void
    {
        $this->templates = EmailTemplate::query()
            ->orderBy('name')
            ->get();
    }

    public function edit(int $id): void
    {
        $template = EmailTemplate::findOrFail($id);

        $this->selectedTemplateId = $template->id;
        $this->name = $template->name;
        $this->description = $template->description ?? '';
        $this->subject = $template->subject;
        $this->body = $template->body;
        $this->is_active = (bool) $template->is_active;
    }

    public function save(): void
    {
        $this->validate();

        $template = EmailTemplate::findOrFail($this->selectedTemplateId);
        $template->update([
            'subject' => $this->subject,
            'body' => $this->body,
            'is_active' => (bool) $this->is_active,
        ]);

        $this->loadTemplates();
        session()->flash('success', 'Email template updated successfully.');
    }

    public function cancel(): void
    {
        $this->selectedTemplateId = null;
        $this->name = '';
        $this->description = '';
        $this->subject = '';
        $this->body = '';
        $this->is_active = true;
    }

    public function render()
    {
        return view('livewire.admin.email-template-manager')
            ->layout('layouts.admin')
            ->title('Email Templates');
    }
}
