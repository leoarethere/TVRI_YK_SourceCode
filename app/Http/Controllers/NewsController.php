<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $title = 'Semua Berita';

        // Logika Judul Dinamis
        if ($request->filled('category')) {
            $category = NewsCategory::firstWhere('slug', $request->category);
            if ($category) {
                $title = 'Berita Kategori: '.$category->name;
            }
        }

        if ($request->filled('author')) {
            $author = User::firstWhere('username', $request->author);
            if ($author) {
                $title = 'Berita oleh: '.$author->name;
            }
        }

        if ($request->filled('search')) {
            $title = 'Pencarian Berita: "'.$request->search.'"';
        }

        // Query Utama
        $query = News::query()
            ->with(['user', 'newsCategory'])
            ->publiclyVisible()
            ->filter($request->only(['search', 'category', 'author']));

        // Sorting
        switch ($request->input('sort')) {
            case 'oldest': $query->orderBy('published_at', 'asc');
                break;
            case 'popular': $query->orderBy('views', 'desc');
                break;
            case 'title_asc': $query->orderBy('title', 'asc');
                break;
            case 'title_desc': $query->orderBy('title', 'desc');
                break;
            default: $query->latest('published_at');
                break;
        }

        // Menggunakan paginate 9 sama seperti posts
        $news = $query->paginate(9)->withQueryString();
        $categories = NewsCategory::whereHas('news', fn ($query) => $query->publiclyVisible())
            ->orderBy('name')
            ->get();

        $authors = User::whereHas('news', fn ($query) => $query->publiclyVisible())
            ->orderBy('name')
            ->get();

        // Arahkan ke view frontend
        return view('frontend.news.index', compact('title', 'news', 'categories', 'authors'));
    }

    public function show(News $news): View
    {
        // Prevent access to unpublished news
        if (! $news->isPubliclyVisible()) {
            abort(404);
        }

        // [OPTIMASI VIEW COUNTER]
        // Gunakan session key berbeda untuk berita: 'news_viewed_'
        $sessionKey = 'news_viewed_'.$news->id;

        if (! session()->has($sessionKey)) {
            $news->incrementViews();
            session()->put($sessionKey, true);
        }

        return view('frontend.news.detail', [
            'title' => $news->title,
            'news' => $news,
        ]);
    }

    // Redirect helper untuk kategori
    public function category(NewsCategory $category): RedirectResponse
    {
        return redirect()->route('berita.index', ['category' => $category->slug]);
    }

    // Redirect helper untuk author
    public function author(User $user): RedirectResponse
    {
        return redirect()->route('berita.index', ['author' => $user->username]);
    }
}
