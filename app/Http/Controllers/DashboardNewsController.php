<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use App\Services\ImageService;
use App\Support\LikeSearch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DashboardNewsController extends Controller
{
    public function __construct(private readonly ImageService $images) {}

    /**
     * Menampilkan daftar berita.
     */
    public function index(Request $request): View
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        // Eager load 'user' dan 'newsCategory' (sesuai nama fungsi relasi di Model News)
        $query = News::with(['user', 'newsCategory']);

        // Filter pencarian
        $query->when($request->filled('search'), function ($q) use ($request) {
            LikeSearch::contains($q, 'title', $request->string('search')->toString());
        });

        // Filter kategori berita
        $query->when($request->filled('category'), function ($q) use ($request) {
            $q->whereHas('newsCategory', function ($subQuery) use ($request) {
                $subQuery->where('slug', $request->category);
            });
        });

        // Filter urutan
        if ($request->input('sort') === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->latest();
        }

        $news = $query->paginate(10)->withQueryString();
        $categories = NewsCategory::orderBy('name')->get();

        // Pastikan Anda nanti membuat view di folder 'backend/news'
        return view('backend.news.index', [
            'posts' => $news, // Kita pakai variabel 'posts' agar mudah copy-paste view index.blade.php yang lama
            'categories' => $categories,
        ]);
    }

    /**
     * Form tambah berita.
     */
    public function create(): View
    {
        $categories = NewsCategory::orderBy('name')->get();

        return view('backend.news.create', compact('categories'));
    }

    /**
     * Simpan berita baru.
     */
    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:news,slug', // Tabel 'news'
                'news_category_id' => 'required|exists:news_categories,id', // Tabel 'news_categories'
                'body' => 'required|string',
                'link_berita' => 'nullable|url|max:255', // Ganti link_postingan jadi link_berita
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
                'excerpt' => 'nullable|string|max:300',
                'published_at' => 'nullable|date',
                'action' => 'required|in:draft,publish',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:160',
            ]);
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }

        $validated['user_id'] = Auth::id();

        $newImagePath = null;
        if ($request->hasFile('featured_image')) {
            try {
                $newImagePath = $this->images->processAndStore(
                    $request->file('featured_image'),
                    'news-images'
                );
                $validated['featured_image'] = $newImagePath;
            } catch (\Exception $e) {
                Log::error('News image error: '.$e->getMessage());

                return back()->withErrors(['featured_image' => 'Gagal memproses gambar.'])->withInput();
            }
        }

        if (empty($validated['excerpt'])) {
            $validated['excerpt'] = Str::limit(strip_tags($validated['body']), 150);
        }

        if ($request->input('action') === 'publish') {
            $validated['status'] = 'published';
            $validated['published_at'] = $request->filled('published_at') ? $request->published_at : now();
        } else {
            $validated['status'] = 'draft';
            $validated['published_at'] = null;
        }

        try {
            News::create($validated);
        } catch (\Exception $e) {
            $this->images->deleteImageQuietly($newImagePath);
            Log::error('News creation error: '.$e->getMessage());

            return back()->withErrors(['error' => 'Gagal menyimpan berita. Silakan coba lagi.'])->withInput();
        }

        $message = $validated['status'] === 'published' ? 'Berita dipublikasikan!' : 'Berita disimpan sebagai draft!';

        // Sesuaikan route redirect ini nanti
        return redirect()->route('dashboard.berita.index')->with('success', $message);
    }

    /**
     * Form edit berita.
     */
    public function edit(News $news): View
    {
        $categories = NewsCategory::orderBy('name')->get();

        return view('backend.news.edit', compact('news', 'categories'));
    }

    /**
     * Tampilkan detail berita (redirect ke halaman publik).
     */
    /**
     * Update berita.
     */
    public function update(Request $request, News $news): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'slug' => [
                    'required', 'string', 'max:255',
                    Rule::unique('news')->ignore($news->id), // Ignore ID berita ini saat cek unique
                ],
                'news_category_id' => 'required|exists:news_categories,id',
                'body' => 'required|string',
                'link_berita' => 'nullable|url|max:255',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
                'excerpt' => 'nullable|string|max:300',
                'published_at' => 'nullable|date',
                'action' => 'required|in:draft,publish',
                'meta_title' => 'nullable|string|max:255',
                'meta_description' => 'nullable|string|max:160',
            ]);
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }

        $oldImagePath = $news->featured_image;
        $newImagePath = null;
        if ($request->hasFile('featured_image')) {
            try {
                $newImagePath = $this->images->processAndStore(
                    $request->file('featured_image'),
                    'news-images'
                );
                $validated['featured_image'] = $newImagePath;
            } catch (\Exception $e) {
                Log::error('News image update error: '.$e->getMessage());

                return back()->withErrors(['featured_image' => 'Gagal update gambar.'])->withInput();
            }
        }

        if (empty($validated['excerpt'])) {
            $validated['excerpt'] = Str::limit(strip_tags($validated['body']), 150);
        }

        if ($request->input('action') === 'publish') {
            $validated['status'] = 'published';
            $validated['published_at'] = $request->filled('published_at')
                ? $request->published_at
                : ($news->published_at ?? now());
        } else {
            $validated['status'] = 'draft';
            $validated['published_at'] = null;
        }

        // Update dengan error handling
        try {
            $news->update($validated);
        } catch (\Exception $e) {
            $this->images->deleteImageQuietly($newImagePath);
            Log::error('News update error: '.$e->getMessage());

            return back()
                ->withErrors(['error' => 'Gagal memperbarui berita. Silakan coba lagi.'])
                ->withInput();
        }

        if ($newImagePath) {
            $this->images->deleteImageQuietly($oldImagePath);
        }

        return redirect()->route('dashboard.berita.index')->with('success', 'Berita berhasil diperbarui!');
    }

    /**
     * Hapus berita.
     */
    public function destroy(News $news): RedirectResponse
    {
        try {
            $imagePath = $news->featured_image;
            $news->delete();
            $this->images->deleteImageQuietly($imagePath);

            return redirect()->route('dashboard.berita.index')->with('success', 'Berita berhasil dihapus!');
        } catch (\Exception $e) {
            Log::error('News deletion error: '.$e->getMessage());

            return redirect()->route('dashboard.berita.index')->with('error', 'Gagal menghapus berita.');
        }
    }
}
