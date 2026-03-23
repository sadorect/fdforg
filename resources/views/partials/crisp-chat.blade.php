@php
    $crispWebsiteId = config('services.crisp.website_id');
    $crispUser = auth()->user();
@endphp

@if (!empty($crispWebsiteId))
    <script>
        window.$crisp = window.$crisp || [];
        window.CRISP_WEBSITE_ID = @json($crispWebsiteId);

        @if ($crispUser)
            window.$crisp.push(['set', 'user:email', @json($crispUser->email)]);
            window.$crisp.push(['set', 'user:nickname', @json($crispUser->name)]);
        @endif

        (function () {
            const documentHead = document.getElementsByTagName('head')[0];
            const script = document.createElement('script');

            script.src = 'https://client.crisp.chat/l.js';
            script.async = true;

            documentHead.appendChild(script);
        })();
    </script>
@endif
