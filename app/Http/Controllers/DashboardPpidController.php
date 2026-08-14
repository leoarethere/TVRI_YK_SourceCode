<?php

namespace App\Http\Controllers;

use App\Models\Ppid;
use App\Support\PublicStorageWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class DashboardPpidController extends Controller
{
    public function index(): View
    {
        $ppids = Ppid::latest()->paginate(10);

        return view('backend.ppid.index', compact('ppids'));
    }

    public function create(): View
    {
        return view('backend.ppid.create');
    }

    public function store(Request $request): RedirectResponse
    {
        // Validasi
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'source_link' => 'required|url|max:500', // ✅ PERBAIKAN: Sesuai dengan database
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_active' => 'nullable',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Upload cover image (opsional)
        if ($request->hasFile('cover_image')) {
            try {
                $image = $request->file('cover_image');
                $imageName = 'ppid-'.Str::random(20).'.jpg';
                $path = 'ppid-covers/'.$imageName;

                $manager = new ImageManager(new Driver);
                $imgProcessed = $manager->read($image);
                $imgProcessed->scale(width: 800);
                $encoded = $imgProcessed->toJpeg(quality: 80);

                // Simpan menggunakan Storage Facade (konsisten dengan update)
                PublicStorageWriter::put($path, (string) $encoded);
                $validated['cover_image'] = $path;

            } catch (\Exception $e) {
                report($e);

                return back()->withErrors(['cover_image' => 'Gagal mengunggah gambar. Silakan coba lagi.'])->withInput();
            }
        }

        Ppid::create($validated);

        return redirect()->route('dashboard.ppid.index')
            ->with('success', 'Dokumen PPID berhasil ditambahkan!');
    }

    public function edit(Ppid $ppid): View
    {
        return view('backend.ppid.edit', compact('ppid'));
    }

    public function update(Request $request, Ppid $ppid): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'source_link' => 'required|url|max:500',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'is_active' => 'nullable',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $oldCoverPath = $ppid->cover_image;

        // [REFACTOR SAFE UPDATE & STANDARDIZE STORAGE]
        if ($request->hasFile('cover_image')) {
            try {
                $image = $request->file('cover_image');
                $imageName = 'ppid-'.Str::random(20).'.jpg';
                $path = 'ppid-covers/'.$imageName;

                $manager = new ImageManager(new Driver);
                $imgProcessed = $manager->read($image);
                $imgProcessed->scale(width: 800);
                $encoded = $imgProcessed->toJpeg(quality: 80);

                // 1. Simpan gambar baru menggunakan Storage Facade
                PublicStorageWriter::put($path, (string) $encoded);

                $validated['cover_image'] = $path;

            } catch (\Exception $e) {
                report($e);

                return back()->withErrors(['cover_image' => 'Gagal mengunggah gambar. Silakan coba lagi.'])->withInput();
            }
        }

        $ppid->update($validated);

        if (isset($validated['cover_image']) && $oldCoverPath) {
            Storage::disk('public')->delete($oldCoverPath);
        }

        return redirect()->route('dashboard.ppid.index')
            ->with('success', 'Data PPID berhasil diperbarui!');
    }

    public function destroy(Ppid $ppid): RedirectResponse
    {
        $coverPath = $ppid->cover_image;
        $ppid->delete();

        if ($coverPath) {
            Storage::disk('public')->delete($coverPath);
        }

        return redirect()->route('dashboard.ppid.index')
            ->with('success', 'Data berhasil dihapus!');
    }
}
