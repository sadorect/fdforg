<?php

namespace App\Providers;

use App\Models\Page;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\VisitLog;
use App\Services\SocialStatsService;
use App\Support\AdminPermissions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        foreach (AdminPermissions::slugs() as $permissionSlug) {
            Gate::define($permissionSlug, function (User $user) use ($permissionSlug): bool {
                return $user->hasPermission($permissionSlug);
            });
        }

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

            $isEnabled = static function (array $settings, string $key, bool $default = true): bool {
                $fallback = $default ? '1' : '0';

                return in_array(
                    strtolower((string) ($settings[$key] ?? $fallback)),
                    ['1', 'true', 'yes', 'on'],
                    true
                );
            };

            $globalSidebarVisible = $isEnabled($siteSettings, 'global_show_media_sidebar', true);

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

            $visibilityByRoute = [
                'home' => 'show_media_sidebar_home',
                'about' => 'show_media_sidebar_about',
                'programs' => 'show_media_sidebar_programs',
                'donations' => 'show_media_sidebar_donations',
                'contact' => 'show_media_sidebar_contact',
                'accessibility' => 'show_media_sidebar_accessibility',
                'blog.index' => 'show_media_sidebar_blog',
                'blog.show' => 'show_media_sidebar_blog',
                'gallery' => 'show_media_sidebar_gallery',
                'events.index' => 'show_media_sidebar_events',
                'events.show' => 'show_media_sidebar_events',
                'events.calendar' => 'show_media_sidebar_events',
                'events.register' => 'show_media_sidebar_events',
                'courses.index' => 'show_media_sidebar_courses',
                'courses.show' => 'show_media_sidebar_courses',
                'courses.lessons.show' => 'show_media_sidebar_courses',
            ];

            if ($routeName !== null && isset($visibilityByRoute[$routeName])) {
                $showMediaSidebar = $isEnabled($siteSettings, $visibilityByRoute[$routeName], true);
            }

            if (Schema::hasTable('pages')) {
                $publishedPageSlugs = Page::published()
                    ->whereIn('slug', ['about', 'programs', 'donations', 'contact', 'accessibility'])
                    ->pluck('slug')
                    ->all();
            }

            if (Schema::hasTable('pages') && Schema::hasColumn('pages', 'show_media_sidebar')) {
                $pageSlugForSidebar = $routeName === 'pages.show'
                    ? request()->route('slug')
                    : null;

                if (! empty($pageSlugForSidebar)) {
                    $pageSidebarSetting = Page::query()
                        ->where('slug', $pageSlugForSidebar)
                        ->value('show_media_sidebar');

                    if ($pageSidebarSetting !== null) {
                        $showMediaSidebar = (bool) $pageSidebarSetting;
                    }
                }
            }

            $mediaSidebarChannels = app(SocialStatsService::class)->buildChannels($siteSettings);

            if (count($mediaSidebarChannels) === 0) {
                $showMediaSidebar = false;
            }

            $view->with([
                'totalSiteVisits' => $totalSiteVisits,
                'publishedPageSlugs' => array_fill_keys($publishedPageSlugs, true),
                'siteBranding' => [
                    'name' => $siteSettings['site_name'] ?? config('app.name'),
                    'logo_url' => ! empty($siteSettings['site_logo_path']) ? asset('storage/'.$siteSettings['site_logo_path']) : null,
                    'favicon_url' => ! empty($siteSettings['site_favicon_path']) ? asset('storage/'.$siteSettings['site_favicon_path']) : null,
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
                    'channels' => $mediaSidebarChannels,
                ],
            ]);
        });
    }
}
