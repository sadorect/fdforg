<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\SiteSetting;
use App\Models\VisitLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts.app', function ($view) {
            $totalSiteVisits = 0;
            $publishedPageSlugs = [];
            $siteSettings = [];
            $showMediaSidebar = false;
            $routeName = request()->route()?->getName();

            if (Schema::hasTable('visit_logs')) {
                $totalSiteVisits = Cache::remember('analytics.total_site_visits', now()->addMinutes(5), function () {
                    return VisitLog::site()->count();
                });
            }

            if (Schema::hasTable('site_settings')) {
                $siteSettings = SiteSetting::allAsKeyValue();
            }

            $globalSidebarVisible = in_array(
                strtolower((string) ($siteSettings['global_show_media_sidebar'] ?? '1')),
                ['1', 'true', 'yes', 'on'],
                true
            );

            $showMediaSidebar = $globalSidebarVisible;

            if (
                $routeName !== null
                && (
                    str_starts_with($routeName, 'dashboard')
                    || str_starts_with($routeName, 'password.')
                    || in_array($routeName, ['login', 'login.submit', 'register', 'register.submit', 'logout'], true)
                )
            ) {
                $showMediaSidebar = false;
            }

            if (Schema::hasTable('pages')) {
                $publishedPageSlugs = Page::published()
                    ->whereIn('slug', ['about', 'programs', 'donations', 'contact', 'accessibility'])
                    ->pluck('slug')
                    ->all();
            }

            if (Schema::hasTable('pages') && Schema::hasColumn('pages', 'show_media_sidebar')) {
                $pageSlugForSidebar = match ($routeName) {
                    'home' => 'home',
                    'about' => 'about',
                    'programs' => 'programs',
                    'donations' => 'donations',
                    'contact' => 'contact',
                    'accessibility' => 'accessibility',
                    'pages.show' => request()->route('slug'),
                    default => null,
                };

                if (!empty($pageSlugForSidebar)) {
                    $pageSidebarSetting = Page::query()
                        ->where('slug', $pageSlugForSidebar)
                        ->value('show_media_sidebar');

                    if ($pageSidebarSetting !== null) {
                        $showMediaSidebar = (bool) $pageSidebarSetting;
                    }
                }
            }

            $gallerySidebarVisible = in_array(
                strtolower((string) ($siteSettings['gallery_show_media_sidebar'] ?? '1')),
                ['1', 'true', 'yes', 'on'],
                true
            );

            if ($routeName === 'gallery') {
                $showMediaSidebar = $gallerySidebarVisible;
            }

            $mediaSidebarStreams = collect([
                ['label' => 'Facebook', 'url' => $siteSettings['social_facebook_url'] ?? null, 'action_text' => 'Follow'],
                ['label' => 'Instagram', 'url' => $siteSettings['social_instagram_url'] ?? null, 'action_text' => 'Follow'],
                ['label' => 'X / Twitter', 'url' => $siteSettings['social_x_url'] ?? null, 'action_text' => 'Follow'],
                ['label' => 'YouTube', 'url' => $siteSettings['social_youtube_url'] ?? null, 'action_text' => 'Watch'],
                ['label' => 'TikTok', 'url' => $siteSettings['social_tiktok_url'] ?? null, 'action_text' => 'Watch'],
                ['label' => 'LinkedIn', 'url' => $siteSettings['social_linkedin_url'] ?? null, 'action_text' => 'Connect'],
            ])->filter(fn (array $stream) => filled($stream['url']))
                ->values()
                ->all();

            $view->with([
                'totalSiteVisits' => $totalSiteVisits,
                'publishedPageSlugs' => array_fill_keys($publishedPageSlugs, true),
                'siteBranding' => [
                    'name' => $siteSettings['site_name'] ?? config('app.name'),
                    'logo_url' => !empty($siteSettings['site_logo_path']) ? asset('storage/' . $siteSettings['site_logo_path']) : null,
                    'favicon_url' => !empty($siteSettings['site_favicon_path']) ? asset('storage/' . $siteSettings['site_favicon_path']) : null,
                ],
                'siteFooter' => [
                    'tagline' => $siteSettings['footer_tagline'] ?? 'Bridging the communication gap and empowering the deaf community through education, advocacy, and support.',
                    'phone' => $siteSettings['footer_phone'] ?? '(555) 123-4567',
                    'email' => $siteSettings['footer_email'] ?? 'info@friendsofthedeaffoundation.org',
                    'address' => $siteSettings['footer_address'] ?? '',
                ],
                'mediaSidebar' => [
                    'show' => $showMediaSidebar,
                    'title' => $siteSettings['media_sidebar_title'] ?? 'Media Streams',
                    'streams' => $mediaSidebarStreams,
                ],
            ]);
        });
    }
}
