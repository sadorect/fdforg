<?php

namespace App\Http\Middleware;

use App\Models\VisitLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class TrackSiteAnalytics
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldTrack($request)) {
            $this->logRequestVisit($request);
        }

        return $next($request);
    }

    private function shouldTrack(Request $request): bool
    {
        if (!$request->isMethod('GET')) {
            return false;
        }

        if ($request->expectsJson() || $request->ajax()) {
            return false;
        }

        if (
            $request->is('admin') ||
            $request->is('admin/*') ||
            $request->is('livewire/*') ||
            $request->is('up')
        ) {
            return false;
        }

        $userAgent = strtolower((string) $request->userAgent());
        if ($userAgent !== '' && preg_match('/bot|crawler|spider|curl|wget/', $userAgent)) {
            return false;
        }

        return true;
    }

    private function logRequestVisit(Request $request): void
    {
        $path = '/' . ltrim($request->path(), '/');
        if ($path === '//') {
            $path = '/';
        }

        $sessionId = $request->hasSession() ? $request->session()->getId() : null;
        $visitDateKey = 'analytics.site_visit_date';
        $today = now()->toDateString();
        $isNewSiteVisit = true;

        if ($request->hasSession()) {
            $lastVisitDate = $request->session()->get($visitDateKey);
            $isNewSiteVisit = $lastVisitDate !== $today;
        }

        $context = [
            'user_id' => $request->user()?->id,
            'path' => $path,
            'route_name' => $request->route()?->getName(),
            'full_url' => $request->fullUrl(),
            'session_id' => $sessionId,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->headers->get('referer'),
            'device_type' => $this->detectDeviceType((string) $request->userAgent()),
            'browser' => $this->detectBrowser((string) $request->userAgent()),
            'is_authenticated' => $request->user() !== null,
            'visited_at' => now(),
        ];

        if ($isNewSiteVisit) {
            VisitLog::create(array_merge($context, ['visit_type' => 'site']));

            if ($request->hasSession()) {
                $request->session()->put($visitDateKey, $today);
            }

            Cache::forget('analytics.total_site_visits');
        }

        VisitLog::create(array_merge($context, ['visit_type' => 'page']));
    }

    private function detectDeviceType(string $userAgent): string
    {
        $ua = strtolower($userAgent);

        if ($ua === '') {
            return 'unknown';
        }

        if (str_contains($ua, 'tablet') || str_contains($ua, 'ipad')) {
            return 'tablet';
        }

        if (str_contains($ua, 'mobile') || str_contains($ua, 'android') || str_contains($ua, 'iphone')) {
            return 'mobile';
        }

        return 'desktop';
    }

    private function detectBrowser(string $userAgent): string
    {
        $ua = strtolower($userAgent);

        if ($ua === '') {
            return 'Unknown';
        }

        if (str_contains($ua, 'edg/')) {
            return 'Edge';
        }

        if (str_contains($ua, 'opr/') || str_contains($ua, 'opera')) {
            return 'Opera';
        }

        if (str_contains($ua, 'firefox/')) {
            return 'Firefox';
        }

        if (str_contains($ua, 'safari/') && !str_contains($ua, 'chrome/')) {
            return 'Safari';
        }

        if (str_contains($ua, 'chrome/')) {
            return 'Chrome';
        }

        if (str_contains($ua, 'msie') || str_contains($ua, 'trident/')) {
            return 'Internet Explorer';
        }

        return 'Other';
    }
}
