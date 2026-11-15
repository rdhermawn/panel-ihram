<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b0b0d">
    <title>@yield('page-title', 'Account') | Etherna Perfect World</title>
    <link rel="icon" href="{{ asset('assets/img/favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Forum&family=Hanken+Grotesk:wght@400;500;600;700&family=Spline+Sans+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/app.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/etherna-landing.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/etherna-auth.css') }}">
</head>
<body class="pw-site pw-auth-site">
    @include('website.partials.header')
    <main class="pw-auth-main @yield('auth-width')">
        <section class="pw-auth-access" aria-label="Account access">
            <div class="pw-auth-content">
                @yield('content')
            </div>
        </section>
    </main>
    <script defer src="{{ asset('assets/js/etherna-landing.js') }}"></script>
</body>
</html>
