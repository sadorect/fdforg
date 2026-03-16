<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Analytics Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        h1 { font-size: 20px; margin-bottom: 4px; }
        h2 { font-size: 14px; margin: 16px 0 6px; }
        p { margin: 4px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 6px; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; vertical-align: top; }
        th { background: #f3f4f6; }
        .stats { width: 100%; margin-top: 8px; }
        .stats td { border: 1px solid #ddd; padding: 8px; width: 50%; }
        .muted { color: #666; }
    </style>
</head>
<body>
    <h1>Site Analytics Report</h1>
    <p class="muted">Generated at: {{ $generatedAt->format('Y-m-d H:i:s') }}</p>
    <p class="muted">Range: Last {{ $rangeDays }} days (from {{ $from->format('Y-m-d') }})</p>
    <p class="muted">Path filter: {{ $pathFilter !== '' ? $pathFilter : 'None' }}</p>

    <table class="stats">
        <tr>
            <td><strong>Site Visits:</strong> {{ number_format($totalSiteVisits) }}</td>
            <td><strong>Page Visits:</strong> {{ number_format($totalPageVisits) }}</td>
        </tr>
        <tr>
            <td><strong>Unique Sessions:</strong> {{ number_format($uniqueSessions) }}</td>
            <td><strong>Authenticated Sessions:</strong> {{ number_format($authenticatedSiteVisits) }}</td>
        </tr>
        <tr>
            <td colspan="2"><strong>Avg Pages Per Site Visit:</strong> {{ number_format($avgPagesPerVisit, 2) }}</td>
        </tr>
    </table>

    <h2>Daily Site Visits</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Visits</th>
            </tr>
        </thead>
        <tbody>
            @forelse($dailySiteVisits as $row)
                <tr>
                    <td>{{ \Illuminate\Support\Carbon::parse($row->day)->format('Y-m-d') }}</td>
                    <td>{{ number_format($row->visits) }}</td>
                </tr>
            @empty
                <tr><td colspan="2">No data</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Top Pages</h2>
    <table>
        <thead>
            <tr>
                <th>Path</th>
                <th>Visits</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topPages as $row)
                <tr>
                    <td>{{ $row->path ?: '/' }}</td>
                    <td>{{ number_format($row->visits) }}</td>
                </tr>
            @empty
                <tr><td colspan="2">No data</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Top Routes</h2>
    <table>
        <thead>
            <tr>
                <th>Route Name</th>
                <th>Visits</th>
            </tr>
        </thead>
        <tbody>
            @forelse($topRoutes as $row)
                <tr>
                    <td>{{ $row->route_name }}</td>
                    <td>{{ number_format($row->visits) }}</td>
                </tr>
            @empty
                <tr><td colspan="2">No data</td></tr>
            @endforelse
        </tbody>
    </table>

    <h2>Recent Visits (Top 100)</h2>
    <table>
        <thead>
            <tr>
                <th>Time</th>
                <th>Type</th>
                <th>Path</th>
                <th>User</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentVisits as $visit)
                <tr>
                    <td>{{ $visit->visited_at?->format('Y-m-d H:i:s') }}</td>
                    <td>{{ strtoupper($visit->visit_type) }}</td>
                    <td>{{ $visit->path }}</td>
                    <td>{{ $visit->user?->email ?? 'Guest' }}</td>
                </tr>
            @empty
                <tr><td colspan="4">No data</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
