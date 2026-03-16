<?php

namespace App\Livewire\Admin;

use App\Models\ContentTransferLog;
use App\Models\Page;
use App\Services\ContentTransferService;
use App\Support\AdminPermissions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\WithFileUploads;

class ContentTransferManager extends AdminComponent
{
    use WithFileUploads;

    protected array $adminAbilities = [
        AdminPermissions::MANAGE_PAGES,
        AdminPermissions::MANAGE_SITE_SETTINGS,
        AdminPermissions::MANAGE_BLOG,
        AdminPermissions::MANAGE_CATEGORIES,
        AdminPermissions::MANAGE_EVENTS,
        AdminPermissions::MANAGE_GALLERY,
        AdminPermissions::MANAGE_COURSES,
        AdminPermissions::MANAGE_LESSONS,
        AdminPermissions::MANAGE_HERO_SLIDES,
        AdminPermissions::MANAGE_EMAIL_TEMPLATES,
    ];

    public $pageTransferUpload;

    public $siteSettingsTransferUpload;

    public $bundleTransferUpload;

    public function render()
    {
        return view('livewire.admin.content-transfer-manager', [
            'pages' => $this->canManagePages()
                ? Page::query()->orderBy('title')->get(['id', 'title', 'slug', 'status', 'updated_at'])
                : collect(),
            'recentTransferLogs' => ContentTransferLog::query()
                ->with('user')
                ->latest()
                ->limit(12)
                ->get(),
            'canManagePages' => $this->canManagePages(),
            'canManageSiteSettings' => $this->canManageSiteSettings(),
            'canManageBlogTransfer' => $this->canManageBlogTransfer(),
            'canManageEventsTransfer' => $this->canManageEventsTransfer(),
            'canManageGalleryTransfer' => $this->canManageGalleryTransfer(),
            'canManageLearningTransfer' => $this->canManageLearningTransfer(),
            'canManageFullSiteTransfer' => $this->canManageFullSiteTransfer(),
        ])->layout('layouts.admin')
            ->title('Content Transfer');
    }

    public function importPageTransfer(): void
    {
        abort_unless($this->canManagePages(), 403);

        $this->validate([
            'pageTransferUpload' => ['required', 'file', 'max:30720'],
        ]);

        $service = app(ContentTransferService::class);

        try {
            $payload = $this->parseUploadedJson($this->pageTransferUpload, 'pageTransferUpload');
            $summary = $service->summarizePackage($payload);

            if (! in_array($summary['type'], ['page', 'pages'], true)) {
                throw ValidationException::withMessages([
                    'pageTransferUpload' => 'Upload a single-page or all-pages transfer file here.',
                ]);
            }

            $importedSlugs = $service->importPagePackage($payload);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            $this->addError('pageTransferUpload', $exception->getMessage());

            return;
        }

        $this->reset('pageTransferUpload');
        $this->recordImport($summary);

        $count = count($importedSlugs);
        session()->flash(
            'success',
            $count === 1
                ? 'Page content imported successfully for ['.$importedSlugs[0].'].'
                : 'Imported '.$count.' pages successfully.'
        );
    }

    public function importSiteSettingsTransfer(): void
    {
        abort_unless($this->canManageSiteSettings(), 403);

        $this->validate([
            'siteSettingsTransferUpload' => ['required', 'file', 'max:30720'],
        ]);

        $service = app(ContentTransferService::class);

        try {
            $payload = $this->parseUploadedJson($this->siteSettingsTransferUpload, 'siteSettingsTransferUpload');
            $summary = $service->summarizePackage($payload);

            if ($summary['type'] !== 'site-settings') {
                throw ValidationException::withMessages([
                    'siteSettingsTransferUpload' => 'Upload a site settings transfer file here.',
                ]);
            }

            $importedCount = $service->importSiteSettingsPackage($payload);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            $this->addError('siteSettingsTransferUpload', $exception->getMessage());

            return;
        }

        $this->reset('siteSettingsTransferUpload');
        $this->recordImport($summary);

        session()->flash('success', 'Imported '.$importedCount.' site settings values successfully.');
    }

    public function importBundleTransfer(): void
    {
        $this->validate([
            'bundleTransferUpload' => ['required', 'file', 'max:51200'],
        ]);

        $service = app(ContentTransferService::class);

        try {
            $payload = $this->parseUploadedJson($this->bundleTransferUpload, 'bundleTransferUpload');
            $summary = $service->summarizePackage($payload);
            $this->authorizeBundleType($summary['type']);
            $service->importBundlePackage($payload, Auth::user());
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            $this->addError('bundleTransferUpload', $exception->getMessage());

            return;
        }

        $this->reset('bundleTransferUpload');
        $this->recordImport($summary);

        session()->flash('success', $summary['label'].' imported successfully.');
    }

    protected function parseUploadedJson($uploadedFile, string $field): array
    {
        $contents = file_get_contents($uploadedFile->getRealPath());

        if (! is_string($contents) || trim($contents) === '') {
            throw ValidationException::withMessages([$field => 'The uploaded file is empty.']);
        }

        try {
            $payload = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw ValidationException::withMessages([$field => 'The uploaded file is not valid JSON.']);
        }

        if (! is_array($payload)) {
            throw ValidationException::withMessages([$field => 'The uploaded file must decode to a JSON object.']);
        }

        return $payload;
    }

    protected function recordImport(array $summary): void
    {
        ContentTransferLog::record(
            Auth::user(),
            'import',
            $summary['type'],
            $summary['item_count'],
            $summary['summary'],
            $summary['details']
        );
    }

    protected function authorizeBundleType(string $type): void
    {
        $allowed = match ($type) {
            ContentTransferService::BUNDLE_BLOG => $this->canManageBlogTransfer(),
            ContentTransferService::BUNDLE_EVENTS => $this->canManageEventsTransfer(),
            ContentTransferService::BUNDLE_GALLERY => $this->canManageGalleryTransfer(),
            ContentTransferService::BUNDLE_LEARNING => $this->canManageLearningTransfer(),
            ContentTransferService::BUNDLE_SITE => $this->canManageFullSiteTransfer(),
            ContentTransferService::BUNDLE_HERO_SLIDES,
            ContentTransferService::BUNDLE_EMAIL_TEMPLATES => $this->canManageFullSiteTransfer(),
            default => false,
        };

        abort_unless($allowed, 403);
    }

    protected function canManagePages(): bool
    {
        return Auth::user()?->hasPermission(AdminPermissions::MANAGE_PAGES) ?? false;
    }

    protected function canManageSiteSettings(): bool
    {
        return Auth::user()?->hasPermission(AdminPermissions::MANAGE_SITE_SETTINGS) ?? false;
    }

    protected function canManageBlogTransfer(): bool
    {
        return Auth::user()?->hasAnyPermission([
            AdminPermissions::MANAGE_BLOG,
            AdminPermissions::MANAGE_CATEGORIES,
        ]) ?? false;
    }

    protected function canManageEventsTransfer(): bool
    {
        return Auth::user()?->hasPermission(AdminPermissions::MANAGE_EVENTS) ?? false;
    }

    protected function canManageGalleryTransfer(): bool
    {
        return Auth::user()?->hasPermission(AdminPermissions::MANAGE_GALLERY) ?? false;
    }

    protected function canManageLearningTransfer(): bool
    {
        return Auth::user()?->hasAnyPermission([
            AdminPermissions::MANAGE_COURSES,
            AdminPermissions::MANAGE_LESSONS,
        ]) ?? false;
    }

    protected function canManageFullSiteTransfer(): bool
    {
        $user = Auth::user();

        if ($user?->isSuperAdmin()) {
            return true;
        }

        if (! $user) {
            return false;
        }

        $requiredPermissions = [
            AdminPermissions::MANAGE_PAGES,
            AdminPermissions::MANAGE_SITE_SETTINGS,
            AdminPermissions::MANAGE_BLOG,
            AdminPermissions::MANAGE_EVENTS,
            AdminPermissions::MANAGE_GALLERY,
            AdminPermissions::MANAGE_COURSES,
            AdminPermissions::MANAGE_LESSONS,
            AdminPermissions::MANAGE_HERO_SLIDES,
            AdminPermissions::MANAGE_EMAIL_TEMPLATES,
        ];

        foreach ($requiredPermissions as $permission) {
            if (! $user->hasPermission($permission)) {
                return false;
            }
        }

        return true;
    }
}
