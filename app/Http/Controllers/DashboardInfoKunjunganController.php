<?php

namespace App\Http\Controllers;

use App\Models\InfoKunjungan;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class DashboardInfoKunjunganController extends Controller
{
    public function index(Request $request)
    {
        $query = InfoKunjungan::query();

        // Pencarian
        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }

        // Sorting
        if ($request->input('sort') === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $items = $query->paginate(10)->withQueryString();

        return view('backend.info-kunjungan.index', compact('items'));
    }

    public function create()
    {
        return view('backend.info-kunjungan.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'source_link' => 'required|url',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // Max 5MB
            'description' => 'required|string',
        ]);

        $data = $validated;
        $data['slug'] = Str::slug($validated['title']).'-'.Str::random(5);
        $data['is_active'] = $request->has('is_active');

        // Upload Cover (dengan kompresi)
        if ($request->hasFile('cover_image')) {
            try {
                $file = $request->file('cover_image');
                $filename = 'kunjungan-covers/'.Str::random(40).'.jpg';

                $manager = new ImageManager(new Driver);
                $image = $manager->read($file);
                $image->scale(width: 800);
                $encoded = $image->toJpeg(quality: 80);

                Storage::disk('public')->put($filename, (string) $encoded);
                $data['cover_image'] = $filename;
            } catch (\Exception $e) {
                report($e);

                return back()->withErrors(['cover_image' => 'Gagal memproses gambar. Silakan coba lagi.'])->withInput();
            }
        }

        InfoKunjungan::create($data);

        return redirect()->route('dashboard.info-kunjungan.index')
            ->with('success', 'Informasi Kunjungan berhasil ditambahkan!');
    }

    public function edit(InfoKunjungan $infoKunjungan)
    {
        return view('backend.info-kunjungan.edit', compact('infoKunjungan'));
    }

    public function update(Request $request, InfoKunjungan $infoKunjungan)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'source_link' => 'required|url',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'description' => 'required|string',
        ]);

        $data = $validated;

        // [PERBAIKAN SEO] Slug tidak diubah saat update judul

        $data['is_active'] = $request->has('is_active');

        $oldCoverPath = $infoKunjungan->cover_image;

        // [PERBAIKAN LOGIKA GAMBAR] Safe Image Update (dengan kompresi)
        if ($request->hasFile('cover_image')) {
            try {
                $file = $request->file('cover_image');
                $filename = 'kunjungan-covers/'.Str::random(40).'.jpg';

                $manager = new ImageManager(new Driver);
                $image = $manager->read($file);
                $image->scale(width: 800);
                $encoded = $image->toJpeg(quality: 80);

                // 1. Simpan gambar baru
                Storage::disk('public')->put($filename, (string) $encoded);

                $data['cover_image'] = $filename;
            } catch (\Exception $e) {
                report($e);

                return back()->withErrors(['cover_image' => 'Gagal memproses gambar. Silakan coba lagi.'])->withInput();
            }
        }

        $infoKunjungan->update($data);

        if (isset($data['cover_image']) && $oldCoverPath) {
            Storage::disk('public')->delete($oldCoverPath);
        }

        return redirect()->route('dashboard.info-kunjungan.index')
            ->with('success', 'Informasi Kunjungan berhasil diperbarui!');
    }

    public function destroy(InfoKunjungan $infoKunjungan)
    {
        $coverPath = $infoKunjungan->cover_image;
        $infoKunjungan->delete();

        if ($coverPath) {
            Storage::disk('public')->delete($coverPath);
        }

        return redirect()->route('dashboard.info-kunjungan.index')
            ->with('success', 'Informasi Kunjungan berhasil dihapus!');
    }
}
