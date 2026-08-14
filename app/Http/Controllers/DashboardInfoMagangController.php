<?php

namespace App\Http\Controllers;

use App\Models\InfoMagang;
use App\Support\PublicStorageWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Illuminate\View\View;

class DashboardInfoMagangController extends Controller
{
    public function index(Request $request): View
    {
        $query = InfoMagang::query();

        // Fitur Pencarian
        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }

        // Fitur Sorting
        if ($request->input('sort') === 'oldest') {
            $query->oldest();
        } else {
            $query->latest();
        }

        $items = $query->paginate(10)->withQueryString();

        return view('backend.info-magang.index', compact('items'));
    }

    public function create(): View
    {
        return view('backend.info-magang.create');
    }

    public function store(Request $request): RedirectResponse
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

        // Handle Upload Cover (dengan kompresi)
        if ($request->hasFile('cover_image')) {
            try {
                $file = $request->file('cover_image');
                $filename = 'magang-covers/'.Str::random(40).'.jpg';

                $manager = new ImageManager(new Driver);
                $image = $manager->read($file);
                $image->scale(width: 800);
                $encoded = $image->toJpeg(quality: 80);

                PublicStorageWriter::put($filename, (string) $encoded);
                $data['cover_image'] = $filename;
            } catch (\Exception $e) {
                report($e);

                return back()->withErrors(['cover_image' => 'Gagal memproses gambar. Silakan coba lagi.'])->withInput();
            }
        }

        InfoMagang::create($data);

        return redirect()->route('dashboard.info-magang.index')
            ->with('success', 'Informasi Magang berhasil ditambahkan!');
    }

    public function edit(InfoMagang $infoMagang): View
    {
        return view('backend.info-magang.edit', compact('infoMagang'));
    }

    public function update(Request $request, InfoMagang $infoMagang): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'source_link' => 'required|url',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'description' => 'required|string',
        ]);

        $data = $validated;

        // [PERBAIKAN SEO] Slug hanya dibuat sekali saat create agar link permanen.

        $data['is_active'] = $request->has('is_active');

        $oldCoverPath = $infoMagang->cover_image;

        // [PERBAIKAN LOGIKA GAMBAR] Upload baru sukses -> Baru hapus lama (dengan kompresi)
        if ($request->hasFile('cover_image')) {
            try {
                $file = $request->file('cover_image');
                $filename = 'magang-covers/'.Str::random(40).'.jpg';

                $manager = new ImageManager(new Driver);
                $image = $manager->read($file);
                $image->scale(width: 800);
                $encoded = $image->toJpeg(quality: 80);

                // 1. Simpan gambar baru
                PublicStorageWriter::put($filename, (string) $encoded);

                $data['cover_image'] = $filename;
            } catch (\Exception $e) {
                report($e);

                return back()->withErrors(['cover_image' => 'Gagal memproses gambar. Silakan coba lagi.'])->withInput();
            }
        }

        $infoMagang->update($data);

        if (isset($data['cover_image']) && $oldCoverPath) {
            Storage::disk('public')->delete($oldCoverPath);
        }

        return redirect()->route('dashboard.info-magang.index')
            ->with('success', 'Informasi Magang berhasil diperbarui!');
    }

    public function destroy(InfoMagang $infoMagang): RedirectResponse
    {
        $coverPath = $infoMagang->cover_image;
        $infoMagang->delete();

        if ($coverPath) {
            Storage::disk('public')->delete($coverPath);
        }

        return redirect()->route('dashboard.info-magang.index')
            ->with('success', 'Informasi Magang berhasil dihapus!');
    }
}
