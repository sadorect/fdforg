<?php

namespace App\Livewire\Admin;

use App\Models\VisitLog;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class AnalyticsDashboard extends Component
{
    use WithPagination;

    public int $rangeDays = 30;
    public string $pathFilter = '';

    protected $queryString = [
        'rangeDays' => ['except' => 30],
        'pathFilter' => ['except' => ''],
    ];

    public function updatedRangeDays(): void
    {
        $this->resetPage();
    }

    public function updatedPathFilter(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $from = now()->subDays($this->rangeDays)->startOfDay();

        $siteQuery = VisitLog::site()->where('visited_at', '>=', $from);
        $pageQuery = VisitLog::page()->where('visited_at', '>=', $from);

        $totalSiteVisits = (clone $siteQuery)->count();
        $totalPageVisits = (clone $pageQuery)->count();
        $uniqueSessions = (clone $siteQuery)->whereNotNull('session_id')->distinct('session_id')->count('session_id');
        $authenticatedSiteVisits = (clone $siteQuery)->where('is_authenticated', true)->count();
        $avgPagesPerVisit = $totalSiteVisits > 0 ? round($totalPageVisits / $totalSiteVisits, 2) : 0.0;

        $sessionPageCounts = VisitLog::page()
            ->where('visited_at', '>=', $from)
            ->whereNotNull('session_id')
            ->selectRaw('session_id, COUNT(*) as page_count')
            ->groupBy('session_id');

        $singlePageSessions = DB::query()->fromSub($sessionPageCounts, 'session_pages')
            ->where('page_count', 1)
            ->count();
        $multiPageSessions = DB::query()->fromSub($sessionPageCounts, 'session_pages')
            ->where('page_count', '>', 1)
            ->count();
        $bounceRate = ($singlePageSessions + $multiPageSessions) > 0
            ? round(($singlePageSessions / ($singlePageSessions + $multiPageSessions)) * 100, 2)
            : 0.0;

        $dailySiteVisits = VisitLog::site()
            ->where('visited_at', '>=', $from)
            ->selectRaw('DATE(visited_at) as day, COUNT(*) as visits')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $topPages = VisitLog::page()
            ->where('visited_at', '>=', $from)
            ->selectRaw('path, COUNT(*) as visits')
            ->groupBy('path')
            ->orderByDesc('visits')
            ->take(10)
            ->get();

        $topRoutes = VisitLog::page()
            ->where('visited_at', '>=', $from)
            ->whereNotNull('route_name')
            ->selectRaw('route_name, COUNT(*) as visits')
            ->groupBy('route_name')
            ->orderByDesc('visits')
            ->take(10)
            ->get();

        $topReferrers = VisitLog::site()
            ->where('visited_at', '>=', $from)
            ->whereNotNull('referrer')
            ->where('referrer', '!=', '')
            ->selectRaw('referrer, COUNT(*) as visits')
            ->groupBy('referrer')
            ->orderByDesc('visits')
            ->take(8)
            ->get();

        $deviceBreakdown = VisitLog::site()
            ->where('visited_at', '>=', $from)
            ->selectRaw('device_type, COUNT(*) as visits')
            ->groupBy('device_type')
            ->orderByDesc('visits')
            ->get();

        $browserBreakdown = VisitLog::site()
            ->where('visited_at', '>=', $from)
            ->selectRaw('browser, COUNT(*) as visits')
            ->groupBy('browser')
            ->orderByDesc('visits')
            ->get();

        $recentVisits = VisitLog::with('user')
            ->where('visited_at', '>=', $from)
            ->when($this->pathFilter !== '', function ($query) {
                $query->where('path', 'like', '%' . $this->pathFilter . '%');
            })
            ->orderByDesc('visited_at')
            ->paginate(15);

        return view('livewire.admin.analytics-dashboard', [
            'totalSiteVisits' => $totalSiteVisits,
            'totalPageVisits' => $totalPageVisits,
            'uniqueSessions' => $uniqueSessions,
            'authenticatedSiteVisits' => $authenticatedSiteVisits,
            'avgPagesPerVisit' => $avgPagesPerVisit,
            'bounceRate' => $bounceRate,
            'singlePageSessions' => $singlePageSessions,
            'multiPageSessions' => $multiPageSessions,
            'dailySiteVisits' => $dailySiteVisits,
            'topPages' => $topPages,
            'topRoutes' => $topRoutes,
            'topReferrers' => $topReferrers,
            'deviceBreakdown' => $deviceBreakdown,
            'browserBreakdown' => $browserBreakdown,
            'recentVisits' => $recentVisits,
        ])->layout('layouts.admin')
            ->title('Analytics');
    }
}
