@extends('website.news.layout', ['pageTitle' => request()->routeIs('news.index') ? 'News' : $heading . ' news'])

@section('content')
    <section class="pw-news-hero">
        <div class="pw-container">
            <p class="pw-eyebrow"><span></span> From the world</p>
            <h1>{{ $heading }} <em>dispatches</em></h1>
            <p>News, events, and updates from the Etherna community.</p>
        </div>
    </section>

    <section class="pw-section pw-news-listing" aria-label="News articles">
        <div class="pw-container">
            <nav class="pw-news-filters" aria-label="Filter news by category">
                <span>Categories</span>
                <a href="{{ route('news.index') }}" @if(request()->routeIs('news.index')) aria-current="page" @endif>All news</a>
                @foreach($categories as $category)
                    <a href="{{ route('show.article.by.category', $category) }}" @if(request()->routeIs('show.article.by.category') && request()->route('category') === $category) aria-current="page" @endif>{{ __('news.category.' . $category) }}</a>
                @endforeach
            </nav>
            @if($tags->isNotEmpty())
                <nav class="pw-news-filters pw-news-tag-filters" aria-label="Filter news by tag">
                    <span>Tags</span>
                    @foreach($tags as $tag)<a href="{{ route('show.article.tag', $tag) }}">#{{ $tag }}</a>@endforeach
                </nav>
            @endif
            <div class="pw-news-grid">
                @forelse($articles as $article)
                    <article class="pw-news-card">
                        <a class="pw-news-image" href="{{ route('show.article', $article->slug) }}">
                            @if($article->og_image)<img src="{{ asset('uploads/og_image/' . $article->og_image) }}" alt="" loading="lazy">@endif
                            <span class="pw-news-category">{{ __('news.category.' . $article->category) }}</span>
                        </a>
                        <div class="pw-news-copy">
                            <time datetime="{{ optional($article->created_at)->toDateString() }}">{{ optional($article->created_at)->format('d M Y') }}</time>
                            <h2><a href="{{ route('show.article', $article->slug) }}">{{ $article->title }}</a></h2>
                            <p>{{ \Illuminate\Support\Str::limit(strip_tags($article->description), 130) }}</p>
                            <a class="pw-read-link" href="{{ route('show.article', $article->slug) }}">Read article <span aria-hidden="true">↗</span></a>
                        </div>
                    </article>
                @empty
                    <p class="pw-empty">No news has been published yet.</p>
                @endforelse
            </div>
            @if($articles->hasPages())
                <nav class="pw-news-pagination" aria-label="News pages">{{ $articles->links() }}</nav>
            @endif
        </div>
    </section>
@endsection
