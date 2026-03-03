<?php

namespace App\Services;

use App\Mail\TemplateNotificationMail;
use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;

class TemplateEmailService
{
    public function send(string $templateKey, string $recipientEmail, array $data = []): void
    {
        EmailTemplate::syncDefaults();

        $template = $this->findActiveTemplate($templateKey);

        if (!$template) {
            return;
        }

        $subject = $this->render($template->subject, $data);
        $body = $this->render($template->body, $data);

        Mail::to($recipientEmail)->send(new TemplateNotificationMail($subject, $body));
    }

    public function renderTemplateBody(string $templateKey, array $data = []): ?string
    {
        EmailTemplate::syncDefaults();

        $template = $this->findActiveTemplate($templateKey);

        if (!$template) {
            return null;
        }

        return $this->render($template->body, $data);
    }

    public function render(string $content, array $data): string
    {
        return preg_replace_callback('/{{\s*([a-zA-Z0-9_]+)\s*}}/', function (array $matches) use ($data) {
            $value = $data[$matches[1]] ?? '';
            return is_scalar($value) ? (string) $value : '';
        }, $content) ?? $content;
    }

    private function findActiveTemplate(string $templateKey): ?EmailTemplate
    {
        return EmailTemplate::query()
            ->where('key', $templateKey)
            ->where('is_active', true)
            ->first();
    }
}
