<?php

namespace App\Http\Controllers;

use App\Models\VisitLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AdminAnalyticsExportController extends Controller
{
    public function exportPdf(Request $request): Response
    {
        $rangeDays = max(1, min(365, (int) $request->query('rangeDays', 30)));
        $pathFilter = trim((string) $request->query('pathFilter', ''));
        $from = now()->subDays($rangeDays)->startOfDay();

        $siteQuery = VisitLog::site()
            ->where('visited_at', '>=', $from)
            ->when($pathFilter !== '', fn ($query) => $query->where('path', 'like', '%' . $pathFilter . '%'));

        $pageQuery = VisitLog::page()
            ->where('visited_at', '>=', $from)
            ->when($pathFilter !== '', fn ($query) => $query->where('path', 'like', '%' . $pathFilter . '%'));

        $totalSiteVisits = (clone $siteQuery)->count();
        $totalPageVisits = (clone $pageQuery)->count();
        $uniqueSessions = (clone $siteQuery)
            ->whereNotNull('session_id')
            ->distinct('session_id')
            ->count('session_id');
        $authenticatedSiteVisits = (clone $siteQuery)->where('is_authenticated', true)->count();
        $avgPagesPerVisit = $totalSiteVisits > 0 ? round($totalPageVisits / $totalSiteVisits, 2) : 0.0;

        $topPages = (clone $pageQuery)
            ->selectRaw('path, COUNT(*) as visits')
            ->groupBy('path')
            ->orderByDesc('visits')
            ->take(20)
            ->get();

        $topRoutes = (clone $pageQuery)
            ->whereNotNull('route_name')
            ->selectRaw('route_name, COUNT(*) as visits')
            ->groupBy('route_name')
            ->orderByDesc('visits')
            ->take(20)
            ->get();

        $dailySiteVisits = (clone $siteQuery)
            ->selectRaw('DATE(visited_at) as day, COUNT(*) as visits')
            ->groupBy('day')
            ->orderBy('day')
            ->get();

        $recentVisits = VisitLog::with('user')
            ->where('visited_at', '>=', $from)
            ->when($pathFilter !== '', fn ($query) => $query->where('path', 'like', '%' . $pathFilter . '%'))
            ->orderByDesc('visited_at')
            ->take(100)
            ->get();

        $pdf = Pdf::loadView('admin.analytics.export-pdf', [
            'generatedAt' => now(),
            'rangeDays' => $rangeDays,
            'pathFilter' => $pathFilter,
            'from' => $from,
            'totalSiteVisits' => $totalSiteVisits,
            'totalPageVisits' => $totalPageVisits,
            'uniqueSessions' => $uniqueSessions,
            'authenticatedSiteVisits' => $authenticatedSiteVisits,
            'avgPagesPerVisit' => $avgPagesPerVisit,
            'topPages' => $topPages,
            'topRoutes' => $topRoutes,
            'dailySiteVisits' => $dailySiteVisits,
            'recentVisits' => $recentVisits,
        ])->setPaper('a4', 'portrait');

        $filename = 'analytics-report-' . now()->format('Ymd-His') . '.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
