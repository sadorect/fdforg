<?php

namespace App\Http\Controllers;

use App\Models\ContentTransferLog;
use App\Models\Page;
use App\Services\ContentTransferService;
use App\Support\AdminPermissions;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AdminContentTransferController extends Controller
{
    public function exportPage(Page $page, ContentTransferService $contentTransferService): StreamedResponse
    {
        abort_unless(auth()->user()?->hasPermission(AdminPermissions::MANAGE_PAGES), Response::HTTP_FORBIDDEN);

        $payload = $contentTransferService->exportPage($page);
        $summary = $contentTransferService->summarizePackage($payload);

        ContentTransferLog::record(
            auth()->user(),
            'export',
            $summary['type'],
            $summary['item_count'],
            $summary['summary'],
            $summary['details']
        );

        return $this->downloadJson(
            $payload,
            'page-'.$page->slug.'-content-transfer-'.now()->format('Ymd-His').'.json'
        );
    }

    public function exportPages(ContentTransferService $contentTransferService): StreamedResponse
    {
        abort_unless(auth()->user()?->hasPermission(AdminPermissions::MANAGE_PAGES), Response::HTTP_FORBIDDEN);

        $payload = $contentTransferService->exportPages();
        $summary = $contentTransferService->summarizePackage($payload);

        ContentTransferLog::record(
            auth()->user(),
            'export',
            $summary['type'],
            $summary['item_count'],
            $summary['summary'],
            $summary['details']
        );

        return $this->downloadJson(
            $payload,
            'pages-content-transfer-'.now()->format('Ymd-His').'.json'
        );
    }

    public function exportSiteSettings(ContentTransferService $contentTransferService): StreamedResponse
    {
        abort_unless(auth()->user()?->hasPermission(AdminPermissions::MANAGE_SITE_SETTINGS), Response::HTTP_FORBIDDEN);

        $payload = $contentTransferService->exportSiteSettings();
        $summary = $contentTransferService->summarizePackage($payload);

        ContentTransferLog::record(
            auth()->user(),
            'export',
            $summary['type'],
            $summary['item_count'],
            $summary['summary'],
            $summary['details']
        );

        return $this->downloadJson(
            $payload,
            'site-settings-transfer-'.now()->format('Ymd-His').'.json'
        );
    }

    public function exportBundle(string $bundle, ContentTransferService $contentTransferService): StreamedResponse
    {
        $this->authorizeBundle($bundle);

        $payload = $contentTransferService->exportBundle($bundle);
        $summary = $contentTransferService->summarizePackage($payload);

        ContentTransferLog::record(
            auth()->user(),
            'export',
            $summary['type'],
            $summary['item_count'],
            $summary['summary'],
            $summary['details']
        );

        return $this->downloadJson(
            $payload,
            $bundle.'-transfer-'.now()->format('Ymd-His').'.json'
        );
    }

    protected function authorizeBundle(string $bundle): void
    {
        $user = auth()->user();

        $isAllowed = match ($bundle) {
            ContentTransferService::BUNDLE_BLOG => $user?->hasAnyPermission([
                AdminPermissions::MANAGE_BLOG,
                AdminPermissions::MANAGE_CATEGORIES,
            ]) ?? false,
            ContentTransferService::BUNDLE_EVENTS => $user?->hasPermission(AdminPermissions::MANAGE_EVENTS) ?? false,
            ContentTransferService::BUNDLE_GALLERY => $user?->hasPermission(AdminPermissions::MANAGE_GALLERY) ?? false,
            ContentTransferService::BUNDLE_LEARNING => $user?->hasAnyPermission([
                AdminPermissions::MANAGE_COURSES,
                AdminPermissions::MANAGE_LESSONS,
            ]) ?? false,
            ContentTransferService::BUNDLE_SITE => $this->canManageFullSiteBundle(),
            default => false,
        };

        abort_unless($isAllowed, Response::HTTP_FORBIDDEN);
    }

    protected function canManageFullSiteBundle(): bool
    {
        $user = auth()->user();

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

    protected function downloadJson(array $payload, string $filename): StreamedResponse
    {
        return response()->streamDownload(function () use ($payload): void {
            echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        }, $filename, [
            'Content-Type' => 'application/json',
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        ]);
    }
}
