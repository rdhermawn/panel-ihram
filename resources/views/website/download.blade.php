<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b0b0d">
    <meta name="description" content="{{ config('download.description') }}">
    <title>{{ config('download.title') }}</title>
    <link rel="icon" href="{{ asset('assets/img/favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Forum&family=Hanken+Grotesk:wght@400;500;600;700&family=Spline+Sans+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/etherna-download.css') }}">
</head>
<body class="pw-download-site">
    <header class="pw-download-header" id="top">
        <div class="pw-download-header-inner">
            <a class="pw-download-brand" href="/" aria-label="{{ config('download.labels.brand') }} {{ config('download.labels.game') }} {{ config('download.navigation.home') }}">
                <img src="{{ asset('assets/img/logo/logokecil.svg') }}" alt="">
                <span><strong>{{ config('download.labels.brand') }}</strong><small>{{ config('download.labels.game') }}</small></span>
            </a>
            <nav class="pw-download-nav" aria-label="{{ config('download.navigation.main_label') }}">
                <a href="/">{{ config('download.navigation.home') }}</a>
                <a href="/download" aria-current="page">{{ config('download.navigation.download') }}</a>
                <a href="/guide">{{ config('download.navigation.guide') }}</a>
                <a href="{{ route('app.ranking.player', 'pvp') }}">{{ config('download.navigation.rankings') }}</a>
            </nav>
            <div class="pw-download-actions">
                <a class="pw-download-login" href="{{ route('login') }}">{{ config('download.navigation.login') }}</a>
                <a class="pw-download-button pw-download-button-small" href="{{ route('register') }}">{{ config('download.navigation.register') }} <span aria-hidden="true">↗</span></a>
                <details class="pw-download-menu">
                    <summary aria-label="{{ config('download.navigation.open_menu') }}"><span></span><span></span></summary>
                    <nav aria-label="{{ config('download.navigation.mobile_label') }}">
                        <a href="/">{{ config('download.navigation.home') }}</a>
                        <a href="/download" aria-current="page">{{ config('download.navigation.download') }}</a>
                        <a href="/guide">{{ config('download.navigation.guide') }}</a>
                        <a href="{{ route('app.ranking.player', 'pvp') }}">{{ config('download.navigation.rankings') }}</a>
                        <a href="{{ route('login') }}">{{ config('download.navigation.login') }}</a>
                        <a href="{{ route('register') }}">{{ config('download.navigation.register') }}</a>
                    </nav>
                </details>
            </div>
        </div>
    </header>

    <main class="pw-download-main">
        <section class="pw-download-intro" aria-labelledby="pw-download-title">
            <div class="pw-download-container">
                <p class="pw-download-breadcrumb">{{ config('download.labels.breadcrumb') }}</p>
                <div class="pw-download-intro-grid">
                    <div>
                        <p class="pw-download-eyebrow">{{ config('download.labels.client_section') }}</p>
                        <h1 id="pw-download-title">{{ config('download.client.title') }}</h1>
                    </div>
                    <p class="pw-download-lede">{{ config('download.intro') }}</p>
                </div>
            </div>
        </section>

        <section class="pw-download-container pw-download-client" aria-labelledby="pw-download-client-title">
            <div class="pw-download-client-copy">
                <p class="pw-download-kicker">{{ config('download.labels.primary_download') }}</p>
                <h2 id="pw-download-client-title">{{ config('download.client.title') }}</h2>
                <p>{{ config('download.client.description') }}</p>
                <div class="pw-download-meta" aria-label="{{ config('download.labels.client_section') }}">
                    <span><small>{{ config('download.labels.platform') }}</small><strong>{{ config('download.client.os') }}</strong></span>
                    <span><small>{{ config('download.labels.size') }}</small><strong>{{ config('download.client.size') }}</strong></span>
                    <span><small>{{ config('download.labels.version') }}</small><strong>{{ config('download.client.version') }}</strong></span>
                </div>
                @if(config('download.client.url'))
                    <a class="pw-download-button pw-download-button-primary" href="{{ config('download.client.url') }}" target="_blank" rel="noopener noreferrer">
                        {{ config('download.client.button_label') }} <span aria-hidden="true">↓</span>
                    </a>
                @else
                    <span class="pw-download-button pw-download-button-primary is-disabled" aria-disabled="true">{{ config('download.client.button_label') }}</span>
                @endif
            </div>
            <div class="pw-download-client-aside">
                <div class="pw-download-current"><span aria-hidden="true"></span>{{ config('download.labels.current') }}</div>
                @if(count(config('download.mirrors', [])))
                    <div class="pw-download-link-group">
                        <h3>{{ config('download.labels.mirrors') }}</h3>
                        @foreach(config('download.mirrors') as $mirror)
                            @if(!empty($mirror['url']))
                                <a class="pw-download-secondary-link" href="{{ $mirror['url'] }}" target="_blank" rel="noopener noreferrer">
                                    <span>{{ $mirror['label'] }}</span><small>{{ $mirror['note'] }}</small><span aria-hidden="true">↗</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
                @if(count(config('download.patches', [])))
                    <div class="pw-download-link-group pw-download-patches">
                        <h3>{{ config('download.labels.patches') }}</h3>
                        @foreach(config('download.patches') as $patch)
                            @if(!empty($patch['url']))
                                <a class="pw-download-patch-link" href="{{ $patch['url'] }}" target="_blank" rel="noopener noreferrer">
                                    <span>{{ $patch['label'] }}</span><small>{{ $patch['note'] }}</small><span aria-hidden="true">↗</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>
        </section>

        <section class="pw-download-container pw-download-steps" aria-labelledby="pw-download-steps-title">
            <div class="pw-download-section-heading">
                <p class="pw-download-kicker">{{ config('download.labels.client_section') }}</p>
                <h2 id="pw-download-steps-title">{{ config('download.labels.steps') }}</h2>
            </div>
            <div class="pw-download-feature-grid">
                @foreach(config('download.features') as $feature)
                    <article class="pw-download-feature">
                        <span class="pw-download-feature-number">{{ $feature['number'] }}</span>
                        <h3>{{ $feature['title'] }}</h3>
                        <p>{{ $feature['text'] }}</p>
                    </article>
                @endforeach
            </div>
        </section>

        <section class="pw-download-components" aria-labelledby="pw-download-components-title">
            <div class="pw-download-container">
                <div class="pw-download-section-heading pw-download-components-heading">
                    <div>
                        <p class="pw-download-kicker">{{ config('download.labels.patcher') }}</p>
                        <h2 id="pw-download-components-title">{{ config('download.components_heading') }}</h2>
                    </div>
                    <p>{{ config('download.components_intro') }}</p>
                </div>
                <div class="pw-download-table-wrap">
                    <table class="pw-download-table">
                        <thead>
                            <tr>
                                <th scope="col">{{ config('download.labels.name') }}</th>
                                <th scope="col">{{ config('download.labels.purpose') }}</th>
                                <th scope="col">{{ config('download.labels.when') }}</th>
                                <th scope="col"><span class="pw-download-sr-only">{{ config('download.labels.download') }}</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(config('download.components') as $component)
                                <tr>
                                    <th scope="row" data-label="{{ config('download.labels.name') }}">
                                        <span>{{ $component['name'] }}</span><small>{{ $component['subtitle'] }}</small>
                                    </th>
                                    <td data-label="{{ config('download.labels.purpose') }}">{{ $component['what'] }}</td>
                                    <td data-label="{{ config('download.labels.when') }}">{{ $component['when'] }}</td>
                                    <td class="pw-download-table-action">
                                        @if(!empty($component['url']))
                                            <a href="{{ $component['url'] }}" target="_blank" rel="noopener noreferrer">{{ config('download.labels.visit') }} <span aria-hidden="true">↗</span><span class="pw-download-sr-only"> — {{ $component['name'] }} ({{ config('download.labels.open_external') }})</span></a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <p class="pw-download-note"><span aria-hidden="true">✳</span>{{ config('download.note') }}</p>
            </div>
        </section>

        <section class="pw-download-support" aria-labelledby="pw-download-support-title">
            <div class="pw-download-container pw-download-support-inner">
                <div>
                    <p class="pw-download-kicker">{{ config('download.labels.support') }}</p>
                    <h2 id="pw-download-support-title">{{ config('download.footer.support_label') }}</h2>
                </div>
                @if(config('download.discord_support'))
                    <a class="pw-download-button pw-download-button-outline" href="{{ config('download.discord_support') }}" target="_blank" rel="noopener noreferrer">{{ config('download.labels.discord') }} <span aria-hidden="true">↗</span></a>
                @endif
            </div>
        </section>
    </main>

    <footer class="pw-download-footer">
        <div class="pw-download-container pw-download-footer-main">
            <a class="pw-download-brand" href="/" aria-label="{{ config('download.labels.brand') }} {{ config('download.labels.game') }} {{ config('download.navigation.home') }}">
                <img src="{{ asset('assets/img/logo/logokecil.svg') }}" alt="">
                <span><strong>{{ config('download.labels.brand') }}</strong><small>{{ config('download.labels.game') }}</small></span>
            </a>
            <p>{{ config('download.footer.tagline') }}</p>
            <nav aria-label="{{ config('download.navigation.footer_label') }}">
                <a href="/download">{{ config('download.navigation.download') }}</a>
                <a href="/guide">{{ config('download.navigation.guide') }}</a>
                <a href="{{ route('app.ranking.player', 'pvp') }}">{{ config('download.navigation.rankings') }}</a>
                @if(config('download.discord_nav'))
                    <a href="{{ config('download.discord_nav') }}" target="_blank" rel="noopener noreferrer">{{ config('download.footer.discord') }} <span aria-hidden="true">↗</span></a>
                @endif
            </nav>
        </div>
        <div class="pw-download-container pw-download-footer-bottom">
            <span>© {{ date('Y') }} {{ config('download.footer.copyright') }}</span>
            <a href="#top">{{ config('download.footer.back_to_top') }} ↑</a>
        </div>
    </footer>
</body>
</html>
