@extends('website.news.layout', ['pageTitle' => $article->title, 'metaDescription' => $article->description])

@section('content')
    <section class="pw-news-hero pw-news-article-heading">
        <div class="pw-container">
            <p class="pw-eyebrow"><span></span> Etherna dispatch</p>
            <h1>{{ $article->title }}</h1>
            <div class="pw-news-meta">
                <time datetime="{{ optional($article->created_at)->toDateString() }}">{{ optional($article->created_at)->format('d M Y') }}</time>
                <a class="pw-news-category" href="{{ route('show.article.by.category', $article->category) }}">{{ __('news.category.' . $article->category) }}</a>
            </div>
        </div>
    </section>

    <article class="pw-container pw-news-article">
        @if($article->og_image)
            <img class="pw-news-feature-image" src="{{ asset('uploads/og_image/' . $article->og_image) }}" alt="">
        @endif
        <div class="pw-news-content">{!! $article->content !!}</div>
        @if($tags)
            <div class="pw-news-tags" aria-label="Article tags">
                @foreach($tags as $tag)<a href="{{ route('show.article.tag', $tag) }}">#{{ $tag }}</a>@endforeach
            </div>
        @endif
        <a class="pw-news-back" href="{{ route('website.news') }}">← All news</a>
    </article>

    @if($moreNews->isNotEmpty())
        <section class="pw-section pw-more-news" aria-labelledby="more-news-title">
            <div class="pw-container">
                <div class="pw-section-heading"><div><p class="pw-eyebrow"><span></span> Keep exploring</p><h2 id="more-news-title">More <em>news</em></h2></div><a class="pw-text-link" href="{{ route('website.news') }}">View all news <span aria-hidden="true">→</span></a></div>
                <div class="pw-news-grid">
                    @foreach($moreNews as $more)
                        <article class="pw-news-card">
                            <a class="pw-news-image" href="{{ route('show.article', $more->slug) }}">
                                @if($more->og_image)<img src="{{ asset('uploads/og_image/' . $more->og_image) }}" alt="" loading="lazy">@endif
                                <span class="pw-news-category">{{ __('news.category.' . $more->category) }}</span>
                            </a>
                            <div class="pw-news-copy"><time datetime="{{ optional($more->created_at)->toDateString() }}">{{ optional($more->created_at)->format('d M Y') }}</time><h2><a href="{{ route('show.article', $more->slug) }}">{{ $more->title }}</a></h2><a class="pw-read-link" href="{{ route('show.article', $more->slug) }}">Read article <span aria-hidden="true">↗</span></a></div>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endsection
