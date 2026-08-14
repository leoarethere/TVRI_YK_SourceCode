<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Support\PublicStorageWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
// ✅ Tambahkan Library Image
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class BannerController extends Controller
{
    /**
     * Menampilkan daftar semua banner.
     */
    public function index(): View
    {
        $banners = Banner::latest()->paginate(10);

        return view('backend.banners.index', compact('banners'));
    }

    /**
     * Menampilkan form untuk membuat banner baru.
     */
    public function create(): View
    {
        return view('backend.banners.create');
    }

    /**
     * Menyimpan banner baru ke database.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string',
            'link' => 'required|url',
            'button_text' => 'nullable|string|max:50',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:10240', // Max 10MB
            'order' => 'nullable|integer',
            'is_active' => 'required|boolean',
        ]);

        // ✅ PROSES KOMPRESI GAMBAR
        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                $filename = 'banners/'.Str::random(40).'.jpg'; // Ubah ekstensi jadi .jpg agar seragam

                // 1. Inisialisasi Image Manager
                $manager = new ImageManager(new Driver);
                $image = $manager->read($file);

                // 2. Resize (Opsional: Banner biasanya lebar, misal max width 1920px)
                $image->scale(width: 1920);

                // 3. Kompresi ke JPEG kualitas 80%
                $encoded = $image->toJpeg(quality: 80);

                // 4. Simpan hasil kompresi
                PublicStorageWriter::put($filename, (string) $encoded);

                $validated['image_path'] = $filename;

            } catch (\Exception $e) {
                report($e);

                return back()->withErrors(['image' => 'Gagal memproses gambar. Silakan coba lagi.'])->withInput();
            }
        }

        Banner::create($validated);

        return redirect()->route('banners.index')->with('success', 'Banner berhasil ditambahkan!');
    }

    /**
     * Menampilkan form edit banner.
     */
    public function edit(Banner $banner): View
    {
        return view('backend.banners.edit', compact('banner'));
    }

    /**
     * Memperbarui banner di database.
     */
    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'required|string',
            'link' => 'required|url',
            'button_text' => 'nullable|string|max:50',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:10240',
            'order' => 'nullable|integer',
            'is_active' => 'required|boolean',
        ]);

        $oldImagePath = $banner->image_path;

        // [REFACTOR SAFE UPDATE]
        if ($request->hasFile('image')) {
            try {
                // 1. Proses Gambar Baru
                $file = $request->file('image');
                $filename = 'banners/'.Str::random(40).'.jpg';

                $manager = new ImageManager(new Driver);
                $image = $manager->read($file);

                // Resize & Compress
                $image->scale(width: 1920);
                $encoded = $image->toJpeg(quality: 80);

                // 2. Simpan Gambar Baru
                PublicStorageWriter::put($filename, (string) $encoded);

                // 3. Update data array
                $validated['image_path'] = $filename;

            } catch (\Exception $e) {
                report($e);

                return back()->withErrors(['image' => 'Gagal memproses gambar. Silakan coba lagi.'])->withInput();
            }
        } else {
            unset($validated['image']);
        }

        $banner->update($validated);

        if (isset($validated['image_path']) && $oldImagePath) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return redirect()->route('banners.index')->with('success', 'Banner berhasil diperbarui!');
    }

    /**
     * Menghapus banner dari database.
     */
    public function destroy(Banner $banner): RedirectResponse
    {
        $banner->delete();

        return redirect()->route('banners.index')->with('success', 'Banner berhasil dihapus!');
    }
}
