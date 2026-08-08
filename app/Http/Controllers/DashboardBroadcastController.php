<?php

namespace App\Http\Controllers;

use App\Models\Broadcast;
use App\Models\BroadcastCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
// 👇 UBAH KE GD AGAR KONSISTEN DENGAN POST CONTROLLER
use Intervention\Image\ImageManager;

class DashboardBroadcastController extends Controller
{
    public function index(Request $request): View
    {
        $query = Broadcast::with(['user', 'broadcastCategory']);

        // Filter Pencarian
        $query->when($request->filled('search'), function ($q) use ($request) {
            $search = str_replace(['%', '_'], ['\%', '\_'], $request->search);
            $q->where(function ($subQuery) use ($search) {
                $subQuery->where('title', 'like', '%'.$search.'%')
                    ->orWhere('synopsis', 'like', '%'.$search.'%');
            });
        });

        // Filter Kategori
        $query->when($request->filled('category'), function ($q) use ($request) {
            $q->whereHas('broadcastCategory', function ($subQuery) use ($request) {
                $subQuery->where('slug', $request->category);
            });
        });

        // Sorting
        if ($request->input('sort') === 'oldest') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->latest();
        }

        $broadcasts = $query->paginate(10)->withQueryString();
        $categories = BroadcastCategory::orderBy('name')->get();

        return view('backend.penyiaran.index', [
            'broadcasts' => $broadcasts,
            'categories' => $categories,
            'broadcastCategories' => $categories,
        ]);
    }

    public function create(): View
    {
        $categories = BroadcastCategory::orderBy('name')->get();

        return view('backend.penyiaran.create', compact('categories'));
    }

    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:broadcasts,slug',
            'broadcast_category_id' => 'required|exists:broadcast_categories,id',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'youtube_link' => 'nullable|url|max:255',
            'synopsis' => 'nullable|string',
            'action' => 'required|in:draft,publish',
            'is_active' => 'required|boolean',
        ]);

        // Tambahkan data user dan konversi boolean
        $validated['user_id'] = Auth::id();
        $validated['is_active'] = $request->boolean('is_active');

        // HAPUS Baris Broadcast::create() yang ada disini sebelumnya (Penyebab Double Insert)

        // 2. Handle Upload Gambar
        if ($request->hasFile('poster')) {
            try {
                $file = $request->file('poster');
                $filename = Str::random(40).'.jpg';
                $path = 'broadcast-posters/'.$filename;

                $manager = new ImageManager(new Driver);
                $image = $manager->read($file);
                $image->scale(width: 800);
                $encodedImage = $image->toJpeg(quality: 75);

                Storage::disk('public')->put($path, (string) $encodedImage);
                $validated['poster'] = $path;

            } catch (\Exception $e) {
                report($e);

                return back()
                    ->withErrors(['poster' => 'Gagal memproses poster. Silakan coba lagi.'])
                    ->withInput();
            }
        }

        // 3. Tentukan Status & Tanggal Publish
        if ($request->input('action') === 'publish') {
            $validated['status'] = 'published';
            $validated['published_at'] = now();
        } else {
            $validated['status'] = 'draft';
            $validated['published_at'] = null;
        }

        // 4. Simpan ke Database (Hanya sekali di sini)
        Broadcast::create($validated);

        $message = $validated['status'] === 'published'
            ? 'Penyiaran berhasil dipublikasikan!'
            : 'Penyiaran disimpan sebagai draft!';

        return redirect()->route('dashboard.penyiaran.index')
            ->with('success', $message);
    }

    public function show(Broadcast $broadcast): RedirectResponse
    {
        return redirect()->route('dashboard.penyiaran.edit', $broadcast);
    }

    public function edit(Broadcast $broadcast): View
    {
        $categories = BroadcastCategory::orderBy('name')->get();

        return view('backend.penyiaran.edit', compact('broadcast', 'categories'));
    }

    public function update(Request $request, Broadcast $broadcast): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => ['required', 'string', 'max:255', Rule::unique('broadcasts')->ignore($broadcast->id)],
            'broadcast_category_id' => 'required|exists:broadcast_categories,id',
            'synopsis' => 'nullable|string',
            'youtube_link' => 'nullable|url|max:255',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'published_at' => 'nullable|date',
            'action' => 'required|in:draft,publish',
            'is_active' => 'required|boolean',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        $oldPosterPath = $broadcast->poster;

        // [REFACTOR SAFE UPDATE]
        if ($request->hasFile('poster')) {
            try {
                // 1. Proses Gambar Baru
                $file = $request->file('poster');
                $filename = Str::random(40).'.jpg';
                $path = 'broadcast-posters/'.$filename;

                $manager = new ImageManager(new Driver);
                $image = $manager->read($file);
                $image->scale(width: 800);
                $encodedImage = $image->toJpeg(quality: 75);

                // 2. Simpan
                Storage::disk('public')->put($path, (string) $encodedImage);

                $validated['poster'] = $path;

            } catch (\Exception $e) {
                report($e);

                return back()->withErrors([
                    'poster' => 'Gagal memproses poster. Silakan coba lagi.',
                ])->withInput();
            }
        }

        // ... (Logika status publish tetap sama) ...
        if ($request->input('action') === 'publish') {
            $validated['status'] = 'published';
            $validated['published_at'] = $request->filled('published_at')
                ? $request->published_at
                : ($broadcast->published_at ?? now());
        } else {
            $validated['status'] = 'draft';
            $validated['published_at'] = null;
        }

        $broadcast->update($validated);

        if (isset($validated['poster']) && $oldPosterPath) {
            Storage::disk('public')->delete($oldPosterPath);
        }

        $message = $validated['status'] === 'published'
            ? 'Penyiaran berhasil diperbarui!'
            : 'Perubahan berhasil disimpan sebagai draft!';

        return redirect()->route('dashboard.penyiaran.index')
            ->with('success', $message);
    }

    public function destroy(Broadcast $broadcast): RedirectResponse
    {
        try {
            $broadcastTitle = $broadcast->title;
            $posterPath = $broadcast->poster;
            $broadcast->delete();

            if ($posterPath) {
                Storage::disk('public')->delete($posterPath);
            }

            return redirect()->route('dashboard.penyiaran.index')
                ->with('success', 'Penyiaran "'.$broadcastTitle.'" berhasil dihapus! 🗑️');

        } catch (\Exception $e) {
            report($e);

            return redirect()->route('dashboard.penyiaran.index')
                ->with('error', 'Gagal menghapus penyiaran. Silakan coba lagi.');
        }
    }
}
