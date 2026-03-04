@props([
    'title' => 'Media Streams',
    'streams' => [],
])

@if(count($streams) > 0)
    <section class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex justify-end">
                <aside class="w-full max-w-xl overflow-hidden rounded-2xl border border-cyan-100 bg-white shadow-lg ring-1 ring-cyan-50">
                    <div class="bg-gradient-to-r from-slate-900 via-blue-800 to-cyan-600 px-5 py-4 text-white">
                        <h3 class="text-sm font-bold uppercase tracking-[0.2em]">{{ $title }}</h3>
                        <p class="mt-1 text-sm text-cyan-100">Stay connected with our latest activities and events.</p>
                    </div>
                    <div class="grid grid-cols-1 gap-2 p-3 sm:grid-cols-2">
                        @foreach($streams as $stream)
                            @php($streamMonogram = strtoupper(substr((string) ($stream['label'] ?? 'S'), 0, 1)))
                            <a
                                href="{{ $stream['url'] }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="group flex items-center justify-between rounded-xl border border-gray-200 bg-gray-50 px-3 py-2.5 text-sm transition hover:-translate-y-0.5 hover:border-cyan-200 hover:bg-cyan-50"
                            >
                                <span class="inline-flex items-center gap-2">
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-cyan-100 text-xs font-bold text-cyan-700">{{ $streamMonogram }}</span>
                                    <span class="font-semibold text-gray-900">{{ $stream['label'] }}</span>
                                </span>
                                <span class="text-xs font-semibold text-cyan-700">{{ $stream['action_text'] ?? 'Open' }}</span>
                            </a>
                        @endforeach
                    </div>
                </aside>
            </div>
        </div>
    </section>
@endif
