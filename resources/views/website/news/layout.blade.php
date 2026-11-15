<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b0b0d">
    <meta name="description" content="{{ $metaDescription ?? 'The latest news, events, and updates from Etherna Perfect World.' }}">
    <title>{{ $pageTitle }} | Etherna Perfect World</title>
    <link rel="icon" href="{{ asset('assets/img/favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Forum&family=Hanken+Grotesk:wght@400;500;600;700&family=Spline+Sans+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/etherna-landing.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/etherna-news.css') }}">
</head>
<body class="pw-site pw-news-site">
    @include('website.partials.header')
    <main class="pw-news-main">
        @yield('content')
    </main>
    <footer class="pw-footer">
        <div class="pw-container pw-footer-main">
            <a class="pw-brand" href="/" aria-label="Etherna Perfect World home"><img src="{{ asset('assets/img/logo/logokecil.svg') }}" alt=""><span><strong>ETHERNA</strong><small>PERFECT WORLD</small></span></a>
            <p>A world of your own.<br>Find your legend among the clouds.</p>
            <nav class="pw-footer-links" aria-label="Footer navigation">
                <a href="/download">Download</a><a href="/guide">Guide</a><a href="{{ route('app.ranking.player', 'pvp') }}">Rankings</a>
                @if(config('pw-config.discord'))<a href="{{ config('pw-config.discord') }}" target="_blank" rel="noopener noreferrer">Discord ↗</a>@endif
            </nav>
        </div>
        <div class="pw-container pw-footer-bottom"><span>© {{ date('Y') }} Etherna Perfect World</span><a href="#top" class="pw-back-top">Back to top ↑</a></div>
    </footer>
    <script defer src="{{ asset('assets/js/etherna-landing.js') }}"></script>
</body>
</html>
