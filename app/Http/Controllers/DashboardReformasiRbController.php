<?php

namespace App\Http\Controllers;

use App\Models\ReformasiRb;
use App\Support\PublicStorageWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Illuminate\View\View;

class DashboardReformasiRbController extends Controller
{
    public function index(Request $request): View
    {
        $query = ReformasiRb::query();

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

        return view('backend.reformasi-rb.index', compact('items'));
    }

    public function create(): View
    {
        return view('backend.reformasi-rb.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'file_link' => 'required|url',
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
                $filename = 'reformasi-covers/'.Str::random(40).'.jpg';

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

        ReformasiRb::create($data);

        return redirect()->route('dashboard.reformasi-rb.index')
            ->with('success', 'Data Reformasi RB berhasil ditambahkan!');
    }

    public function edit(ReformasiRb $reformasiRb): View
    {
        return view('backend.reformasi-rb.edit', compact('reformasiRb'));
    }

    public function update(Request $request, ReformasiRb $reformasiRb): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'file_link' => 'required|url',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'description' => 'required|string',
        ]);

        $data = $validated;

        // [PERBAIKAN SEO] Slug dijaga tetap permanen

        $data['is_active'] = $request->has('is_active');

        $oldCoverPath = $reformasiRb->cover_image;

        // [PERBAIKAN LOGIKA GAMBAR] Safe Image Update (dengan kompresi)
        if ($request->hasFile('cover_image')) {
            try {
                $file = $request->file('cover_image');
                $filename = 'reformasi-covers/'.Str::random(40).'.jpg';

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

        $reformasiRb->update($data);

        if (isset($data['cover_image']) && $oldCoverPath) {
            Storage::disk('public')->delete($oldCoverPath);
        }

        return redirect()->route('dashboard.reformasi-rb.index')
            ->with('success', 'Data Reformasi RB berhasil diperbarui!');
    }

    public function destroy(ReformasiRb $reformasiRb): RedirectResponse
    {
        $coverPath = $reformasiRb->cover_image;
        $reformasiRb->delete();

        if ($coverPath) {
            Storage::disk('public')->delete($coverPath);
        }

        return redirect()->route('dashboard.reformasi-rb.index')
            ->with('success', 'Data Reformasi RB berhasil dihapus!');
    }
}
