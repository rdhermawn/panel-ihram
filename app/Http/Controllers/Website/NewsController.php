<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\News;


class NewsController extends Controller
{
    public function index()
    {
        return $this->listing(News::query(), 'News');
    }

    public function indexCategory(string $category)
    {
        return $this->listing(News::where('category', $category), __('news.category.' . $category));
    }

    public function indexTags(string $tag)
    {
        return $this->listing(News::whereRaw("FIND_IN_SET(?, REPLACE(LOWER(keywords), ' ', ''))", [str_replace(' ', '', mb_strtolower($tag))]), '#' . $tag);
    }

    public function showPost(string $slug)
    {
        $article = News::where('slug', $slug)->firstOrFail();

        return view('website.news.article', [
            'article' => $article,
            'tags' => array_filter(array_map('trim', explode(',', (string) $article->keywords))),
            'moreNews' => News::where('id', '!=', $article->id)->latest()->take(3)->get(),
        ]);
    }

    private function listing($query, string $heading)
    {
        $articles = $query->latest()->paginate(9)->withQueryString();
        $tags = $articles->getCollection()
            ->flatMap(fn ($article) => explode(',', (string) $article->keywords))
            ->map(fn ($tag) => trim($tag))
            ->filter()
            ->unique(fn ($tag) => mb_strtolower($tag))
            ->values();

        return view('website.news.index', [
            'articles' => $articles,
            'categories' => ['update', 'maintenance', 'event', 'contest', 'download', 'guide', 'luckybox', 'other'],
            'heading' => $heading,
            'tags' => $tags,
        ]);
    }
}
