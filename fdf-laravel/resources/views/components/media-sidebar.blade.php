@props([
    'title' => 'Media Streams',
    'streams' => [],
])

@if(count($streams) > 0)
    <aside class="fixed right-4 top-24 z-20 hidden w-72 xl:block">
        <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg">
            <div class="bg-gradient-to-r from-blue-700 to-cyan-600 px-4 py-3 text-white">
                <h3 class="text-sm font-bold uppercase tracking-wide">{{ $title }}</h3>
                <p class="mt-1 text-xs text-cyan-100">Follow our latest activities across all platforms.</p>
            </div>
            <div class="space-y-2 p-3">
                @foreach($streams as $stream)
                    <a
                        href="{{ $stream['url'] }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="flex items-center justify-between rounded-lg border border-gray-200 bg-gray-50 px-3 py-2 text-sm transition hover:border-blue-200 hover:bg-blue-50"
                    >
                        <span class="font-semibold text-gray-900">{{ $stream['label'] }}</span>
                        <span class="text-xs text-blue-600">{{ $stream['action_text'] ?? 'Open' }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </aside>
@endif
