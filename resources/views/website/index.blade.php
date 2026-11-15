<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#0b0b0d">
    <meta name="description" content="Enter the world of Etherna Perfect World. Download the client, explore the guide, and follow the latest player rankings.">
    <title>Etherna Perfect World</title>
    <link rel="icon" href="{{ asset('assets/img/favicon.svg') }}" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Forum&family=Hanken+Grotesk:wght@400;500;600;700&family=Spline+Sans+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/css/etherna-landing.css') }}">
    <script defer src="{{ asset('assets/js/etherna-landing.js') }}"></script>
</head>
<body class="pw-site">
    <div id="pw-preloader" class="pw-preloader" aria-label="Loading Etherna Perfect World" role="status">
        <span class="pw-loader-mark" aria-hidden="true">E</span>
        <span class="pw-loader-line" aria-hidden="true"></span>
    </div>

    <header class="pw-header" id="top">
        <div class="pw-header-inner">
            <a class="pw-brand" href="/" aria-label="Etherna Perfect World home">
                <img src="{{ asset('assets/img/logo/logokecil.svg') }}" alt="">
                <span><strong>ETHERNA</strong><small>PERFECT WORLD</small></span>
            </a>
            <nav class="pw-nav" aria-label="Main navigation">
                <a href="/">Home</a>
                <a href="/download">Download</a>
                <a href="/guide">Guide</a>
                <a href="{{ route('app.ranking.player', 'pvp') }}">Rankings</a>
            </nav>
            <div class="pw-header-actions">
                <a class="pw-login" href="{{ route('login') }}">Log in</a>
                <a class="pw-button pw-button-small" href="{{ route('register') }}">Create account <span aria-hidden="true">↗</span></a>
                <button class="pw-nav-toggle" type="button" aria-label="Open navigation" aria-expanded="false" aria-controls="pw-mobile-nav">
                    <span></span><span></span>
                </button>
            </div>
        </div>
        <nav class="pw-mobile-nav" id="pw-mobile-nav" aria-label="Mobile navigation" hidden>
            <a href="/">Home</a>
            <a href="/download">Download</a>
            <a href="/guide">Guide</a>
            <a href="{{ route('app.ranking.player', 'pvp') }}">Rankings</a>
            <a href="{{ route('login') }}">Log in</a>
            <a href="{{ route('register') }}">Create account</a>
        </nav>
    </header>

    <main>
        <section class="pw-hero" aria-labelledby="pw-hero-title">
            <div class="pw-hero-image" aria-hidden="true"></div>
            <div class="pw-hero-content pw-container">
                <p class="pw-eyebrow"><span></span> A world beyond the horizon</p>
                <h1 class="pw-hero-title" id="pw-hero-title">Your legend<br>begins <em>here.</em></h1>
                <p class="pw-hero-subtitle"><span aria-hidden="true"></span> PERFECT WORLD · MMORPG</p>
                <p class="pw-hero-tagline">Forge your path, find your people, and make a place for yourself in Etherna.</p>
                <div class="pw-hero-actions">
                    <a class="pw-button" href="/download">Enter the world <span aria-hidden="true">↗</span></a>
                    @if(config('pw-config.discord'))
                        <span class="pw-hero-divider" aria-hidden="true"></span>
                        <a class="pw-social-button" href="{{ config('pw-config.discord') }}" target="_blank" rel="noopener noreferrer" aria-label="Join the Etherna Discord">
                            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5.5 6.5h13A1.5 1.5 0 0 1 20 8v7a1.5 1.5 0 0 1-1.5 1.5h-4.1L10 20v-3.5H5.5A1.5 1.5 0 0 1 4 15V8a1.5 1.5 0 0 1 1.5-1.5Z"></path><circle cx="9" cy="11.5" r="1"></circle><circle cx="15" cy="11.5" r="1"></circle></svg>
                        </a>
                    @endif
                </div>
            </div>
        </section>

        <section class="pw-stats pw-container" aria-label="Server information">
            {{-- PLACEHOLDER server metadata: replace these four values with confirmed live details. --}}
            <div class="pw-stat"><span class="pw-stat-label pw-stat-label-online"><i aria-hidden="true"></i>Online</span><span class="pw-stat-value">1,000+</span></div>
            <div class="pw-stat"><span class="pw-stat-label">Version</span><span class="pw-stat-value">v1.0</span></div>
            <div class="pw-stat"><span class="pw-stat-label">Wipes</span><span class="pw-stat-value">TBA</span></div>
            <div class="pw-stat"><span class="pw-stat-label">Region</span><span class="pw-stat-value">SEA</span></div>
        </section>

        <section class="pw-section pw-news-section" id="news" aria-labelledby="news-title">
            <div class="pw-container">
                <div class="pw-section-heading" data-reveal>
                    <div><p class="pw-eyebrow"><span></span> From the world</p><h2 id="news-title">Latest <em>dispatches</em></h2></div>
                    <p>News, events, and updates from the Etherna community.</p>
                </div>
                <div class="pw-news-grid">
                    @forelse($news as $article)
                        <article class="pw-news-card" data-reveal>
                            <a class="pw-news-image" href="{{ route('show.article', $article->slug) }}">
                                <img src="{{ $article->og_image ? asset('uploads/og_image/' . $article->og_image) : asset('assets/img/home-6/news/news-1.jpg') }}" alt="" loading="lazy">
                                <span class="pw-news-category">{{ __('news.category.' . $article->category) }}</span>
                            </a>
                            <div class="pw-news-copy">
                                <time datetime="{{ optional($article->created_at)->toDateString() }}">{{ optional($article->created_at)->format('d M Y') }}</time>
                                <h3><a href="{{ route('show.article', $article->slug) }}">{{ $article->title }}</a></h3>
                                <p>{{ \Illuminate\Support\Str::limit(strip_tags($article->description), 112) }}</p>
                                <a class="pw-read-link" href="{{ route('show.article', $article->slug) }}">Read dispatch <span aria-hidden="true">↗</span></a>
                            </div>
                        </article>
                    @empty
                        <p class="pw-empty">No news has been published yet.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="pw-section pw-world-section" aria-labelledby="world-title">
            <div class="pw-container">
                <div class="pw-section-heading" data-reveal>
                    <div><p class="pw-eyebrow"><span></span> Your next chapter</p><h2 id="world-title">A world to <em>make your own</em></h2></div>
                    <p>Everything you need to begin your journey and find your place in Etherna.</p>
                </div>
                <div class="pw-feature-grid">
                    <a class="pw-feature-card" href="/download" data-reveal><span class="pw-feature-number">01</span><span class="pw-feature-icon" aria-hidden="true">↓</span><h3>Get the client</h3><p>Download the game and prepare to enter the world.</p><span class="pw-feature-arrow" aria-hidden="true">↗</span></a>
                    <a class="pw-feature-card" href="/guide" data-reveal><span class="pw-feature-number">02</span><span class="pw-feature-icon" aria-hidden="true">⌘</span><h3>Find your way</h3><p>Explore the guide before taking your first steps.</p><span class="pw-feature-arrow" aria-hidden="true">↗</span></a>
                    <a class="pw-feature-card" href="{{ route('app.ranking.player', 'pvp') }}" data-reveal><span class="pw-feature-number">03</span><span class="pw-feature-icon" aria-hidden="true">♜</span><h3>Meet the legends</h3><p>See who stands at the top of the PvP rankings.</p><span class="pw-feature-arrow" aria-hidden="true">↗</span></a>
                    <a class="pw-feature-card" href="#news" data-reveal><span class="pw-feature-number">04</span><span class="pw-feature-icon" aria-hidden="true">✳</span><h3>Stay in the know</h3><p>Catch the latest updates and community events.</p><span class="pw-feature-arrow" aria-hidden="true">↗</span></a>
                    <a class="pw-feature-card" href="{{ route('register') }}" data-reveal><span class="pw-feature-number">05</span><span class="pw-feature-icon" aria-hidden="true">◇</span><h3>Make it yours</h3><p>Create an account and start writing your story.</p><span class="pw-feature-arrow" aria-hidden="true">↗</span></a>
                </div>
            </div>
        </section>

        <section class="pw-section pw-rankings-section" aria-labelledby="ranking-title">
            <div class="pw-container">
                <div class="pw-section-heading" data-reveal>
                    <div><p class="pw-eyebrow"><span></span> The names to know</p><h2 id="ranking-title">PvP <em>rankings</em></h2></div>
                    <a class="pw-text-link" href="{{ route('app.ranking.player', 'pvp') }}">View all rankings <span aria-hidden="true">→</span></a>
                </div>
                <div class="pw-rankings" data-reveal>
                    <div class="pw-list-heading pw-pvp-heading"><span>#</span><span>{{ __('ranking.name') }}</span><span>{{ __('ranking.class') }}</span><span>{{ __('ranking.gender.caption') }}</span><span>{{ __('ranking.spouse.caption') }}</span><span>{{ __('ranking.type.pvp') }}</span><span>{{ __('ranking.guild') }}</span></div>
                    @forelse($ranks as $rank)
                        @php
                            $classIcons = [0 => 'blademaster', 1 => 'mage', 2 => 'psychic', 3 => 'venomancer', 4 => 'barbarian', 5 => 'assassin', 6 => 'archer', 7 => 'cleric', 8 => 'seeker', 9 => 'mystic', 10 => 'duskblade', 11 => 'stormbringer', 12 => 'technician', 13 => 'edgerunner', 14 => 'wildwalker'];
                            $classId = $rank->cls;
                            $classKey = $classIcons[$classId] ?? 'unknown';
                            $spouse = $rank->spouse ? $players->getPlayer($rank->spouse) : null;
                        @endphp
                        <div class="pw-rank-row pw-pvp-row">
                            <span class="pw-rank-number">{{ $loop->iteration }}</span>
                            <span class="pw-rank-name">{{ $rank->name }}</span>
                            <span class="pw-rank-class" data-label="{{ __('ranking.class') }}"><img src="{{ asset('img/icon/' . $classKey . '.png') }}" alt="{{ $classKey }}" loading="lazy" title="{{ __('general.classes.' . $classId) }}"><span>{{ __('general.classes.' . $classId) }}</span></span>
                            <span class="pw-rank-gender" data-label="{{ __('ranking.gender.caption') }}"><img src="/img/gender/{{ $rank->gender }}.png" alt="{{ $rank->gender }}" loading="lazy" title="{{ __('ranking.gender.' . $rank->gender) }}"></span>
                            <span class="pw-rank-spouse" data-label="{{ __('ranking.spouse.caption') }}">{{ $spouse ? $spouse->name : __('ranking.spouse.4everalone') }}</span>
                            <strong class="pw-rank-score" data-label="{{ __('ranking.type.pvp') }}">{{ $rank->pk_count }}</strong>
                            <span class="pw-rank-faction" data-label="{{ __('ranking.guild') }}">{{ $rank->faction_name ?? '-' }}</span>
                        </div>
                    @empty
                        <p class="pw-empty pw-empty-row">No player ranking data is available yet.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <section class="pw-section pw-supporters-section" aria-labelledby="supporters-title">
            <div class="pw-container">
                <div class="pw-section-heading" data-reveal>
                    <div><p class="pw-eyebrow"><span></span> The keepers of Etherna</p><h2 id="supporters-title">Community <em>supporters</em></h2></div>
                    <p>{{ \Carbon\Carbon::create($tahun, $bulan, 1)->translatedFormat('F Y') }} · thank you for keeping the world alive.</p>
                </div>
                <div class="pw-supporters-layout">
                    <div class="pw-supporters-note" data-reveal>
                        <span class="pw-supporters-mark" aria-hidden="true">E</span>
                        <p class="pw-eyebrow"><span></span> This month</p>
                        <h3>A world built<br>together.</h3>
                        <p>Our community makes every new chapter possible.</p>
                        <div class="pw-support-total"><span>Community support</span><strong>Rp {{ number_format($totalMoneyAllSpenders, 0, ',', '.') }}</strong></div>
                    </div>
                    <div class="pw-rankings pw-supporters-list" data-reveal>
                        <div class="pw-list-heading"><span>RANK / SUPPORTER</span><span>STREAMER</span><span>CONTRIBUTION</span></div>
                        @forelse($topSpenders as $spender)
                            <div class="pw-spender-row">
                                <span class="pw-rank-player"><span class="pw-rank-number">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><span class="pw-rank-name">{{ \Illuminate\Support\Str::limit($spender->name, 25, '') }}</span></span>
                                <span class="pw-spender-streamer">{{ $spender->streamer_name ?? '—' }}</span>
                                <strong class="pw-rank-score">Rp {{ number_format($spender->total_money, 0, ',', '.') }}</strong>
                            </div>
                        @empty
                            <p class="pw-empty pw-empty-row">No supporters have been listed this month.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>

        <section class="pw-cta" aria-labelledby="cta-title">
            <div class="pw-cta-art" aria-hidden="true"></div>
            <div class="pw-container pw-cta-content" data-reveal>
                <p class="pw-eyebrow"><span></span> The gates are open</p>
                <h2 id="cta-title">A new story<br>is <em>waiting.</em></h2>
                <a class="pw-button" href="{{ route('register') }}">Create your account <span aria-hidden="true">↗</span></a>
            </div>
        </section>
    </main>

    <footer class="pw-footer">
        <div class="pw-container pw-footer-main">
            <a class="pw-brand" href="/" aria-label="Etherna Perfect World home">
                <img src="{{ asset('assets/img/logo/logokecil.svg') }}" alt="">
                <span><strong>ETHERNA</strong><small>PERFECT WORLD</small></span>
            </a>
            <p>A world of your own.<br>Find your legend among the clouds.</p>
            <nav class="pw-footer-links" aria-label="Footer navigation">
                <a href="/download">Download</a><a href="/guide">Guide</a><a href="{{ route('app.ranking.player', 'pvp') }}">Rankings</a>
                @if(config('pw-config.discord'))<a href="{{ config('pw-config.discord') }}" target="_blank" rel="noopener noreferrer">Discord ↗</a>@endif
            </nav>
        </div>
        <div class="pw-container pw-footer-bottom"><span>© {{ date('Y') }} Etherna Perfect World</span><a href="#top" class="pw-back-top">Back to top ↑</a></div>
    </footer>
    <a class="pw-top" href="#top" aria-label="Scroll to top">↑</a>
</body>
</html>
