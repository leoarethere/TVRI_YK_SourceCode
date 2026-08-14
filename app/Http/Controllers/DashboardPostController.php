<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
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

// ✅ GUNAKAN GD DRIVER - Lebih universal dan tersedia di hampir semua server
class DashboardPostController extends Controller
{
    public function __construct(private readonly ImageService $images) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $query = Post::with(['user', 'category']);

        // Filter pencarian
        $query->when($request->filled('search'), function ($q) use ($request) {
            LikeSearch::contains($q, 'title', $request->string('search')->toString());
        });

        // Filter kategori
        $query->when($request->filled('category'), function ($q) use ($request) {
            $q->whereHas('category', function ($subQuery) use ($request) {
                $subQuery->where('slug', $request->category);
            });
        });

        // Filter urutan (sort)
        if ($request->input('sort') === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->latest(); // Default adalah 'latest'
        }

        $posts = $query->paginate(10)->withQueryString();
        $categories = Category::orderBy('name')->get();

        return view('backend.postingan.index', [
            'posts' => $posts,
            'categories' => $categories,
        ]);
    }

    /**
     * Menampilkan form untuk membuat postingan baru.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('backend.postingan.create', compact('categories'));
    }

    /**
     * Menyimpan postingan baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        // ✅ VALIDASI dengan error handling yang lebih baik
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'slug' => 'required|string|max:255|unique:posts,slug',
                'category_id' => 'required|exists:categories,id',
                'body' => 'required|string',
                'link_postingan' => 'nullable|url|max:255',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240', // Max 10MB
                'excerpt' => 'nullable|string|max:300',
                'published_at' => 'nullable|date',
                'action' => 'required|in:draft,publish',
                'meta_title' => 'nullable|string|max:255',       // [TAMBAHAN]
                'meta_description' => 'nullable|string|max:160', // [TAMBAHAN] SEO standard max 160 chars
            ]);
        } catch (ValidationException $e) {
            // Redirect kembali dengan error validation
            return back()->withErrors($e->validator)->withInput();
        }

        $validated['user_id'] = Auth::id();

        // ✅ Process & Compress Image dengan error handling
        $newImagePath = null;
        if ($request->hasFile('featured_image')) {
            try {
                $newImagePath = $this->images->processAndStore(
                    $request->file('featured_image'),
                    'post-images'
                );
                $validated['featured_image'] = $newImagePath;
            } catch (\Exception $e) {
                Log::error('Image processing error: '.$e->getMessage());

                return back()
                    ->withErrors(['featured_image' => 'Gagal memproses gambar. Silakan coba lagi.'])
                    ->withInput();
            }
        }

        // Auto-generate excerpt if empty
        if (empty($validated['excerpt'])) {
            $validated['excerpt'] = Str::limit(strip_tags($validated['body']), 150);
        }

        // Set status based on action
        if ($request->input('action') === 'publish') {
            $validated['status'] = 'published';
            $validated['published_at'] = $request->filled('published_at')
                ? $request->published_at
                : now();
        } else {
            $validated['status'] = 'draft';
            $validated['published_at'] = null;
        }

        // ✅ Simpan dengan error handling
        try {
            Post::create($validated);
        } catch (\Exception $e) {
            $this->images->deleteImageQuietly($newImagePath);
            Log::error('Post creation error: '.$e->getMessage());

            return back()
                ->withErrors(['error' => 'Gagal menyimpan postingan. Silakan coba lagi.'])
                ->withInput();
        }

        $message = $validated['status'] === 'published'
            ? 'Postingan berhasil dipublikasikan!'
            : 'Postingan berhasil disimpan sebagai draft!';

        return redirect()->route('dashboard.postingan.index')->with('success', $message);
    }

    /**
     * Display the specified resource.
     */
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post): View
    {
        $categories = Category::orderBy('name')->get();

        return view('backend.postingan.edit', compact('post', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Post $post): RedirectResponse
    {
        // ✅ VALIDASI dengan error handling
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'slug' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('posts')->ignore($post->id),
                ],
                'category_id' => 'required|exists:categories,id',
                'body' => 'required|string',
                'link_postingan' => 'nullable|url|max:255',
                'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
                'excerpt' => 'nullable|string|max:300',
                'published_at' => 'nullable|date',
                'action' => 'required|in:draft,publish',
                'meta_title' => 'nullable|string|max:255',       // [TAMBAHAN]
                'meta_description' => 'nullable|string|max:160', // [TAMBAHAN] SEO standard max 160 chars
            ]);
        } catch (ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }

        // ✅ Process & Compress New Image
        $oldImagePath = $post->featured_image;
        $newImagePath = null;
        if ($request->hasFile('featured_image')) {
            try {
                $newImagePath = $this->images->processAndStore(
                    $request->file('featured_image'),
                    'post-images'
                );
                $validated['featured_image'] = $newImagePath;
            } catch (\Exception $e) {
                Log::error('Image processing error: '.$e->getMessage());

                return back()
                    ->withErrors(['featured_image' => 'Gagal memproses gambar. Silakan coba lagi.'])
                    ->withInput();
            }
        }

        // Auto-generate excerpt if empty
        if (empty($validated['excerpt'])) {
            $validated['excerpt'] = Str::limit(strip_tags($validated['body']), 150);
        }

        // Set status based on action
        if ($request->input('action') === 'publish') {
            $validated['status'] = 'published';
            $validated['published_at'] = $request->filled('published_at')
                ? $request->published_at
                : ($post->published_at ?? now());
        } else {
            $validated['status'] = 'draft';
            $validated['published_at'] = null;
        }

        // ✅ Update dengan error handling
        try {
            $post->update($validated);
        } catch (\Exception $e) {
            $this->images->deleteImageQuietly($newImagePath);
            Log::error('Post update error: '.$e->getMessage());

            return back()
                ->withErrors(['error' => 'Gagal memperbarui postingan. Silakan coba lagi.'])
                ->withInput();
        }

        if ($newImagePath) {
            $this->images->deleteImageQuietly($oldImagePath);
        }

        $message = $validated['status'] === 'published'
            ? 'Postingan berhasil diperbarui!'
            : 'Perubahan berhasil disimpan sebagai draft!';

        return redirect()->route('dashboard.postingan.index')
            ->with('success', $message);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post): RedirectResponse
    {
        try {
            $postTitle = $post->title;

            $imagePath = $post->featured_image;
            $post->delete();
            $this->images->deleteImageQuietly($imagePath);

            return redirect()->route('dashboard.postingan.index')
                ->with('success', 'Postingan "'.$postTitle.'" berhasil dihapus! 🗑️');

        } catch (\Exception $e) {
            Log::error('Post deletion error: '.$e->getMessage());

            return redirect()->route('dashboard.postingan.index')
                ->with('error', 'Gagal menghapus postingan. Silakan coba lagi.');
        }
    }
}
