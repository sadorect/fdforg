<div class="space-y-6">
    @php($canExportAnalytics = auth()->user()?->hasPermission(\App\Support\AdminPermissions::EXPORT_ANALYTICS))
    <div class="flex flex-wrap items-end justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Site Analytics</h1>
            <p class="text-sm text-gray-600">Traffic insights, engagement trends, and behavior breakdowns.</p>
        </div>
        <div class="flex flex-wrap items-end gap-3 rounded-lg bg-white p-3 shadow">
            <div>
                <label class="text-xs font-semibold uppercase text-gray-500">Range</label>
                <select wire:model.live="rangeDays" class="mt-1 rounded-md border-gray-300 bg-gray-50 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500">
                    <option value="7">Last 7 days</option>
                    <option value="30">Last 30 days</option>
                    <option value="90">Last 90 days</option>
                </select>
            </div>
            <div>
                <label class="text-xs font-semibold uppercase text-gray-500">Path filter</label>
                <input wire:model.live.debounce.300ms="pathFilter" type="text" placeholder="/blog" class="mt-1 rounded-md border-gray-300 bg-gray-50 text-sm text-gray-900 placeholder-gray-400 focus:border-blue-500 focus:ring-blue-500">
            </div>
            @if($canExportAnalytics)
                <div>
                    <a href="{{ route('admin.analytics.export.pdf', ['rangeDays' => $rangeDays, 'pathFilter' => $pathFilter]) }}"
                       class="inline-flex rounded-md bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700">
                        Export PDF
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm text-gray-500">Site Visits</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($totalSiteVisits) }}</p>
            <p class="text-xs text-gray-500">Unique entry sessions</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm text-gray-500">Page Visits</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($totalPageVisits) }}</p>
            <p class="text-xs text-gray-500">{{ number_format($avgPagesPerVisit, 2) }} pages per site visit</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm text-gray-500">Unique Sessions</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($uniqueSessions) }}</p>
            <p class="text-xs text-gray-500">{{ number_format($authenticatedSiteVisits) }} authenticated sessions</p>
        </div>
        <div class="rounded-lg bg-white p-5 shadow">
            <p class="text-sm text-gray-500">Estimated Bounce Rate</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">{{ number_format($bounceRate, 2) }}%</p>
            <p class="text-xs text-gray-500">{{ $singlePageSessions }} single-page vs {{ $multiPageSessions }} multi-page sessions</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-2">
        <div class="rounded-lg bg-white p-5 shadow">
            <h2 class="text-lg font-semibold text-gray-900">Daily Site Visit Trend</h2>
            <p class="text-xs text-gray-500">Daily unique site entries for selected date range.</p>
            <div class="mt-4 space-y-2">
                @php($maxDaily = (int) ($dailySiteVisits->max('visits') ?? 0))
                @forelse($dailySiteVisits as $day)
                    <div>
                        <div class="mb-1 flex items-center justify-between text-xs text-gray-600">
                            <span>{{ \Illuminate\Support\Carbon::parse($day->day)->format('M j, Y') }}</span>
                            <span>{{ $day->visits }}</span>
                        </div>
                        <div class="h-2 rounded bg-gray-100">
                            <div class="h-2 rounded bg-blue-500" style="width: {{ $maxDaily > 0 ? round(($day->visits / $maxDaily) * 100, 2) : 0 }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No visit data yet.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-lg bg-white p-5 shadow">
            <h2 class="text-lg font-semibold text-gray-900">Top Visited Pages</h2>
            <p class="text-xs text-gray-500">Most visited public URLs.</p>
            <div class="mt-4 overflow-hidden rounded border border-gray-100">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-600">Path</th>
                            <th class="px-3 py-2 text-right text-xs font-semibold uppercase text-gray-600">Visits</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @forelse($topPages as $row)
                            <tr>
                                <td class="px-3 py-2 text-sm text-gray-700">{{ $row->path ?: '/' }}</td>
                                <td class="px-3 py-2 text-right text-sm font-semibold text-gray-900">{{ number_format($row->visits) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="px-3 py-4 text-center text-sm text-gray-500">No page visits captured yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">
        <div class="rounded-lg bg-white p-5 shadow">
            <h2 class="text-lg font-semibold text-gray-900">Top Routes</h2>
            <p class="text-xs text-gray-500">Most triggered Laravel route names.</p>
            <div class="mt-3 space-y-2">
                @forelse($topRoutes as $route)
                    <div class="flex items-center justify-between rounded bg-gray-50 px-3 py-2">
                        <span class="text-sm text-gray-700">{{ $route->route_name }}</span>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($route->visits) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No route analytics yet.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-lg bg-white p-5 shadow">
            <h2 class="text-lg font-semibold text-gray-900">Traffic Sources</h2>
            <p class="text-xs text-gray-500">Referrer domains for incoming traffic.</p>
            <div class="mt-3 space-y-2">
                @forelse($topReferrers as $referrer)
                    <div class="flex items-center justify-between rounded bg-gray-50 px-3 py-2">
                        <span class="max-w-[14rem] truncate text-sm text-gray-700">{{ $referrer->referrer }}</span>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($referrer->visits) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-gray-500">No referrer data captured.</p>
                @endforelse
            </div>
        </div>

        <div class="rounded-lg bg-white p-5 shadow">
            <h2 class="text-lg font-semibold text-gray-900">Devices and Browsers</h2>
            <p class="text-xs text-gray-500">Client technology split for site visits.</p>
            <div class="mt-3">
                <h3 class="text-xs font-semibold uppercase text-gray-500">Devices</h3>
                <div class="mt-2 space-y-2">
                    @forelse($deviceBreakdown as $device)
                        <div class="flex items-center justify-between rounded bg-gray-50 px-3 py-2">
                            <span class="text-sm text-gray-700">{{ ucfirst($device->device_type ?: 'unknown') }}</span>
                            <span class="text-sm font-semibold text-gray-900">{{ number_format($device->visits) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No device data.</p>
                    @endforelse
                </div>
            </div>
            <div class="mt-4">
                <h3 class="text-xs font-semibold uppercase text-gray-500">Browsers</h3>
                <div class="mt-2 space-y-2">
                    @forelse($browserBreakdown as $browser)
                        <div class="flex items-center justify-between rounded bg-gray-50 px-3 py-2">
                            <span class="text-sm text-gray-700">{{ $browser->browser ?: 'Unknown' }}</span>
                            <span class="text-sm font-semibold text-gray-900">{{ number_format($browser->visits) }}</span>
                        </div>
                    @empty
                        <p class="text-sm text-gray-500">No browser data.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="rounded-lg bg-white p-5 shadow">
        <h2 class="text-lg font-semibold text-gray-900">Recent Visit Log</h2>
        <p class="text-xs text-gray-500">Latest tracked requests with user, path, source, and client metadata.</p>
        <div class="mt-4 overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-600">Time</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-600">Type</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-600">Path</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-600">User</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-600">Source</th>
                        <th class="px-3 py-2 text-left text-xs font-semibold uppercase text-gray-600">Client</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($recentVisits as $visit)
                        <tr>
                            <td class="px-3 py-2 text-sm text-gray-700">{{ $visit->visited_at?->format('Y-m-d H:i:s') }}</td>
                            <td class="px-3 py-2 text-sm">
                                <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $visit->visit_type === 'site' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                    {{ strtoupper($visit->visit_type) }}
                                </span>
                            </td>
                            <td class="px-3 py-2 text-sm text-gray-700">{{ $visit->path }}</td>
                            <td class="px-3 py-2 text-sm text-gray-700">{{ $visit->user?->email ?? 'Guest' }}</td>
                            <td class="px-3 py-2 text-sm text-gray-700">{{ $visit->referrer ?: 'Direct' }}</td>
                            <td class="px-3 py-2 text-sm text-gray-700">{{ $visit->browser }} / {{ $visit->device_type }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-4 text-center text-sm text-gray-500">No visits match the current filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">
            {{ $recentVisits->links() }}
        </div>
    </div>
</div>
