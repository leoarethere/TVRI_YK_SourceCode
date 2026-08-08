<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class PostController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Semua Postingan';

        // Logika Judul Dinamis
        if ($request->filled('category')) {
            $category = Category::firstWhere('slug', $request->category);
            if ($category) {
                $title = 'Postingan di Kategori: '.$category->name;
            }
        }

        if ($request->filled('author')) {
            $author = User::firstWhere('username', $request->author);
            if ($author) {
                $title = 'Postingan oleh: '.$author->name;
            }
        }

        if ($request->filled('search')) {
            $title = 'Hasil Pencarian: "'.$request->search.'"';
        }

        // Menggunakan ScopeFilter dari Model (Pastikan model Post sudah memiliki scopeFilter)
        $query = Post::query()
            ->with(['user', 'category'])
            ->where('status', 'published')
            ->filter($request->only(['search', 'category', 'author']));

        // Logika Sorting
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

        $posts = $query->paginate(9)->withQueryString();
        $categories = Category::whereHas('posts', fn ($query) => $query->where('status', 'published'))
            ->orderBy('name')
            ->get();
        $authors = User::whereHas('posts', fn ($query) => $query->where('status', 'published'))
            ->orderBy('name')
            ->get();

        return view('frontend.postingan.posts', compact('title', 'posts', 'categories', 'authors'));
    }

    public function show(Post $post)
    {
        // Prevent access to unpublished posts
        if ($post->status !== 'published') {
            abort(404);
        }

        // [OPTIMASI VIEW COUNTER]
        // Gunakan session untuk mencegah perhitungan berulang saat refresh (F5)
        $sessionKey = 'post_viewed_'.$post->id;

        if (! session()->has($sessionKey)) {
            // increment() lebih efisien & atomik daripada update(['views' => ...])
            $post->incrementViews();

            // Simpan penanda di session bahwa user ini sudah melihat post ini
            session()->put($sessionKey, true);
        }

        return view('frontend.postingan.detail', [
            'title' => $post->title,
            'post' => $post,
        ]);
    }

    public function category(Category $category)
    {
        return redirect()->route('postingan.index', ['category' => $category->slug]);
    }

    public function author(User $user)
    {
        return redirect()->route('postingan.index', ['author' => $user->username]);
    }
}
