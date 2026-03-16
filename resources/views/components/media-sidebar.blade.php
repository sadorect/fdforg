@props([
    'title' => 'Media Streams',
    'channels' => [],
])

@if(count($channels) > 0)
    <aside id="floating-media-sidebar" class="fixed right-4 z-30 hidden w-80 xl:block">
        <div class="overflow-hidden rounded-2xl border border-cyan-100 bg-white shadow-xl ring-1 ring-cyan-50">
            <div class="bg-gradient-to-r from-slate-900 via-blue-800 to-cyan-600 px-5 py-4 text-white">
                <h3 class="text-sm font-bold uppercase tracking-[0.2em]">{{ $title }}</h3>
                <p class="mt-1 text-xs text-cyan-100">Live social activity and audience snapshots.</p>
            </div>
            <div class="max-h-[70vh] space-y-2 overflow-y-auto p-3">
                @foreach($channels as $channel)
                    <details class="group rounded-xl border border-gray-200 bg-gray-50">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-3 py-2.5">
                            <span class="inline-flex items-center gap-2">
                                <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-cyan-100 text-xs font-bold text-cyan-700">
                                    {{ strtoupper(substr((string) ($channel['label'] ?? 'S'), 0, 1)) }}
                                </span>
                                <span>
                                    <span class="block text-sm font-semibold text-gray-900">{{ $channel['label'] }}</span>
                                    <span class="block text-[11px] text-gray-500">{{ $channel['primary_label'] ?? 'Status' }}</span>
                                </span>
                            </span>
                            <span class="text-right">
                                <span class="block text-sm font-bold {{ ($channel['status'] ?? 'disconnected') === 'connected' ? 'text-cyan-700' : 'text-gray-500' }}">
                                    {{ $channel['primary_value'] ?? 'N/A' }}
                                </span>
                                <span class="block text-[10px] uppercase tracking-wide {{ ($channel['status'] ?? 'disconnected') === 'connected' ? 'text-emerald-600' : 'text-amber-600' }}">
                                    {{ ($channel['status'] ?? 'disconnected') === 'connected' ? 'Live' : 'Setup Needed' }}
                                </span>
                            </span>
                        </summary>

                        <div class="space-y-2 border-t border-gray-200 px-3 pb-3 pt-2">
                            @if(!empty($channel['details'] ?? []))
                                <div class="space-y-1">
                                    @foreach(($channel['details'] ?? []) as $detail)
                                        <p class="flex items-center justify-between text-xs text-gray-700">
                                            <span>{{ $detail['label'] ?? '' }}</span>
                                            <span class="font-semibold text-gray-900">{{ $detail['value'] ?? 'N/A' }}</span>
                                        </p>
                                    @endforeach
                                </div>
                            @endif

                            @if(!empty($channel['message'] ?? null))
                                <p class="text-[11px] leading-relaxed text-gray-500">{{ $channel['message'] }}</p>
                            @endif

                            @if(!empty($channel['url'] ?? null))
                                <a
                                    href="{{ $channel['url'] }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="inline-flex rounded-md bg-cyan-50 px-2.5 py-1 text-xs font-semibold text-cyan-700 hover:bg-cyan-100"
                                >
                                    {{ $channel['action_text'] ?? 'Open' }}
                                </a>
                            @endif
                        </div>
                    </details>
                @endforeach
            </div>
        </div>
    </aside>
@endif
