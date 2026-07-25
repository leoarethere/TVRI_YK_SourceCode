<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Models\NewsCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;

class DashboardNewsController extends Controller
{
    /**
     * Menampilkan daftar berita.
     */
    public function index(Request $request): View
    {
        // Eager load 'user' dan 'newsCategory' (sesuai nama fungsi relasi di Model News)
        $query = News::with(['user', 'newsCategory']);

        // Filter pencarian
        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = str_replace(['%', '_'], ['\%', '\_'], $request->search);
            $q->where('title', 'like', '%' . $search . '%');
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }

        $validated['user_id'] = Auth::id();

        // Process Image
        if ($request->hasFile('featured_image')) {
            try {
                $file = $request->file('featured_image');
                $filename = Str::random(40) . '.jpg';
                $path = 'news-images/' . $filename; // Folder khusus berita

                $manager = new ImageManager(new Driver());
                $image = $manager->read($file);
                $image->scale(width: 1200);
                $encodedImage = $image->toJpeg(quality: 70);

                Storage::disk('public')->put($path, (string) $encodedImage);
                $validated['featured_image'] = $path;
                
            } catch (\Exception $e) {
                Log::error('News image error: ' . $e->getMessage());
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
            return back()->withErrors(['error' => 'Gagal menyimpan berita: ' . $e->getMessage()])->withInput();
        }

        $message = $validated['status'] === 'published' ? 'Berita dipublikasikan!' : 'Berita disimpan sebagai draft!';
        
        // Sesuaikan route redirect ini nanti
        return redirect()->route('dashboard.berita.index')->with('success', $message);
    }

    /**
     * Form edit berita.
     */
    public function edit(News $news) // Route Model Binding ke 'News'
    {
        $categories = NewsCategory::orderBy('name')->get();
        return view('backend.news.edit', compact('news', 'categories'));
    }

    /**
     * Tampilkan detail berita (redirect ke halaman publik).
     */
    public function show(News $news)
    {
        return redirect()->route('berita.show', $news);
    }

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
                    Rule::unique('news')->ignore($news->id) // Ignore ID berita ini saat cek unique
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }

        if ($request->hasFile('featured_image')) {
            try {
                if ($news->featured_image && Storage::disk('public')->exists($news->featured_image)) {
                    Storage::disk('public')->delete($news->featured_image);
                }

                $file = $request->file('featured_image');
                $filename = Str::random(40) . '.jpg';
                $path = 'news-images/' . $filename;

                $manager = new ImageManager(new Driver());
                $image = $manager->read($file);
                $image->scale(width: 1200);
                $encodedImage = $image->toJpeg(quality: 70);

                Storage::disk('public')->put($path, (string) $encodedImage);
                $validated['featured_image'] = $path;
            } catch (\Exception $e) {
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
            \Illuminate\Support\Facades\Log::error('News update error: ' . $e->getMessage());
            
            return back()
                ->withErrors(['error' => 'Gagal memperbarui berita: ' . $e->getMessage()])
                ->withInput();
        }

        return redirect()->route('dashboard.berita.index')->with('success', 'Berita berhasil diperbarui!');
    }

    /**
     * Hapus berita.
     */
    public function destroy(News $news): RedirectResponse
    {
        try {
            if ($news->featured_image && Storage::disk('public')->exists($news->featured_image)) {
                Storage::disk('public')->delete($news->featured_image);
            }
            $news->delete();
            return redirect()->route('dashboard.berita.index')->with('success', 'Berita berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('dashboard.berita.index')->with('error', 'Gagal menghapus berita.');
        }
    }
}