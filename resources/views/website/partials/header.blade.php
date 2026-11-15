{{-- Shared header for the public site (home, download, news). Edit the menu here only. --}}
@php
    $navLinks = [
        ['label' => 'Home', 'url' => url('/'), 'active' => request()->is('/')],
        ['label' => 'Download', 'url' => url('/download'), 'active' => request()->is('download')],
        ['label' => 'News', 'url' => route('website.news'), 'active' => request()->is('news', 'news/*')],
        ['label' => 'Rankings', 'url' => route('app.ranking.player', 'pvp'), 'active' => false],
    ];
@endphp
<header class="pw-header" id="top">
    <div class="pw-header-inner">
        <a class="pw-brand" href="{{ url('/') }}" aria-label="Etherna Perfect World home">
            <img src="{{ asset('assets/img/logo/logokecil.svg') }}" alt="">
            <span><strong>ETHERNA</strong><small>PERFECT WORLD</small></span>
        </a>
        <nav class="pw-nav" aria-label="Main navigation">
            @foreach($navLinks as $link)
                <a href="{{ $link['url'] }}" @if($link['active']) aria-current="page" @endif>{{ $link['label'] }}</a>
            @endforeach
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
        @foreach($navLinks as $link)
            <a href="{{ $link['url'] }}" @if($link['active']) aria-current="page" @endif>{{ $link['label'] }}</a>
        @endforeach
        <a href="{{ route('login') }}">Log in</a>
        <a href="{{ route('register') }}">Create account</a>
    </nav>
</header>
