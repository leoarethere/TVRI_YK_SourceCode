<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\ImageManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

// ✅ GUNAKAN GD DRIVER - Lebih universal dan tersedia di hampir semua server
use Intervention\Image\Drivers\Gd\Driver;

class DashboardPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = Post::with(['user', 'category']);

        // Filter pencarian
        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = str_replace(['%', '_'], ['\%', '\_'], $request->search);
            $q->where('title', 'like', '%' . $search . '%');
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Redirect kembali dengan error validation
            return back()->withErrors($e->validator)->withInput();
        }

        $validated['user_id'] = Auth::id();

        // ✅ Process & Compress Image dengan error handling
        if ($request->hasFile('featured_image')) {
            try {
                $file = $request->file('featured_image');
                $filename = Str::random(40) . '.jpg';
                $path = 'post-images/' . $filename;

                // Create ImageManager instance dengan GD Driver
                $manager = new ImageManager(new Driver());
                
                // Read and process image
                $image = $manager->read($file);
                
                // Optional: Resize if too large (max width 1200px)
                $image->scale(width: 1200);
                
                // Encode to JPEG with 70% quality
                $encodedImage = $image->toJpeg(quality: 70);

                // Save to storage
                Storage::disk('public')->put($path, (string) $encodedImage);

                $validated['featured_image'] = $path;
                
            } catch (\Exception $e) {
                // Log error untuk debugging
                Log::error('Image processing error: ' . $e->getMessage());
                
                return back()
                    ->withErrors(['featured_image' => 'Gagal memproses gambar: ' . $e->getMessage()])
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
            Log::error('Post creation error: ' . $e->getMessage());
            
            return back()
                ->withErrors(['error' => 'Gagal menyimpan postingan: ' . $e->getMessage()])
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
    public function show(Post $post)
    {
        return redirect()->route('postingan.show', $post);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Post $post)
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
                    Rule::unique('posts')->ignore($post->id)
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
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput();
        }

        // ✅ Process & Compress New Image
        if ($request->hasFile('featured_image')) {
            try {
                // Delete old image
                if ($post->featured_image && Storage::disk('public')->exists($post->featured_image)) {
                    Storage::disk('public')->delete($post->featured_image);
                }

                $file = $request->file('featured_image');
                $filename = Str::random(40) . '.jpg';
                $path = 'post-images/' . $filename;

                // Create ImageManager instance dengan GD Driver
                $manager = new ImageManager(new Driver());
                
                // Read and process image
                $image = $manager->read($file);
                
                // Optional: Resize if too large (max width 1200px)
                $image->scale(width: 1200);
                
                // Encode to JPEG with 70% quality
                $encodedImage = $image->toJpeg(quality: 70);

                // Save to storage
                Storage::disk('public')->put($path, (string) $encodedImage);

                $validated['featured_image'] = $path;
                
            } catch (\Exception $e) {
                Log::error('Image processing error: ' . $e->getMessage());
                
                return back()
                    ->withErrors(['featured_image' => 'Gagal memproses gambar: ' . $e->getMessage()])
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
            Log::error('Post update error: ' . $e->getMessage());
            
            return back()
                ->withErrors(['error' => 'Gagal memperbarui postingan: ' . $e->getMessage()])
                ->withInput();
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

            // Delete featured image if exists
            if ($post->featured_image && Storage::disk('public')->exists($post->featured_image)) {
                Storage::disk('public')->delete($post->featured_image);
            }

            $post->delete();

            return redirect()->route('dashboard.postingan.index')
                ->with('success', 'Postingan "' . $postTitle . '" berhasil dihapus! 🗑️');
                
        } catch (\Exception $e) {
            Log::error('Post deletion error: ' . $e->getMessage());
            
            return redirect()->route('dashboard.postingan.index')
                ->with('error', 'Gagal menghapus postingan. Terjadi error: ' . $e->getMessage());
        }
    }
}