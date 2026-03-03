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

            if (Schema::hasTable('visit_logs')) {
                $totalSiteVisits = Cache::remember('analytics.total_site_visits', now()->addMinutes(5), function () {
                    return VisitLog::site()->count();
                });
            }

            if (Schema::hasTable('site_settings')) {
                $siteSettings = SiteSetting::allAsKeyValue();
            }

            if (Schema::hasTable('pages')) {
                $publishedPageSlugs = Page::published()
                    ->whereIn('slug', ['about', 'programs', 'donations', 'contact', 'accessibility'])
                    ->pluck('slug')
                    ->all();
            }

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
            ]);
        });
    }
}
