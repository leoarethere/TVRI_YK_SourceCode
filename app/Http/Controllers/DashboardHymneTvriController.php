<?php

namespace App\Http\Controllers;

use App\Models\HymneTvri;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
// Pastikan menggunakan GD Driver agar konsisten & stabil
use Intervention\Image\ImageManager;

class DashboardHymneTvriController extends Controller
{
    /**
     * Menampilkan daftar himne.
     */
    public function index(): View
    {
        $hymnes = HymneTvri::latest()->paginate(10);

        return view('backend.hymne-tvri.index', compact('hymnes'));
    }

    /**
     * Form tambah himne.
     */
    public function create(): View
    {
        return view('backend.hymne-tvri.create');
    }

    /**
     * Menyimpan data himne baru.
     */
    public function store(Request $request): RedirectResponse
    {
        // 1. Validasi Input
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'info' => 'nullable|string|max:255',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // Max 5MB
            'synopsis' => 'required|string',
            'link' => 'nullable|url',
            // 'is_active' tidak perlu divalidasi 'required', karena checkbox HTML
        ]);

        // [PERBAIKAN BUG 1] Ambil nilai boolean dari checkbox
        // Jika dicentang = true (1), jika tidak = false (0)
        $validated['is_active'] = $request->has('is_active');

        // 2. Handle Upload Poster
        if ($request->hasFile('poster')) {
            try {
                $file = $request->file('poster');
                $filename = 'hymne/'.Str::random(40).'.jpg';

                // Inisialisasi Image Manager dengan GD Driver
                $manager = new ImageManager(new Driver);
                $image = $manager->read($file);

                // Resize agar tidak terlalu berat (lebar max 600px cukup untuk poster)
                $image->scale(width: 600);

                // Encode ke JPG kualitas 80%
                $encoded = $image->toJpeg(quality: 80);

                // Simpan ke storage
                Storage::disk('public')->put($filename, (string) $encoded);

                $validated['poster'] = $filename;

            } catch (\Exception $e) {
                report($e);

                return back()
                    ->withErrors(['poster' => 'Gagal memproses gambar. Silakan coba lagi.'])
                    ->withInput();
            }
        }

        // 3. Simpan ke Database
        HymneTvri::create($validated);

        return redirect()->route('dashboard.hymne-tvri.index')
            ->with('success', 'Data Himne berhasil ditambahkan!');
    }

    /**
     * Form edit himne.
     */
    public function edit(HymneTvri $hymneTvri): View
    {
        return view('backend.hymne-tvri.edit', compact('hymneTvri'));
    }

    /**
     * Memperbarui data himne.
     */
    public function update(Request $request, HymneTvri $hymneTvri): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'info' => 'nullable|string|max:255',
            'poster' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'synopsis' => 'required|string',
            'link' => 'nullable|url',
        ]);

        // [PERBAIKAN BUG 1] Pastikan status aktif terupdate
        $validated['is_active'] = $request->has('is_active');

        $oldPosterPath = $hymneTvri->poster;

        // [PERBAIKAN BUG 2] Safe Image Replacement Logic
        if ($request->hasFile('poster')) {
            try {
                // A. Proses upload gambar BARU terlebih dahulu
                $file = $request->file('poster');
                $filename = 'hymne/'.Str::random(40).'.jpg';

                $manager = new ImageManager(new Driver);
                $image = $manager->read($file);
                $image->scale(width: 600);
                $encoded = $image->toJpeg(quality: 80);

                // Simpan gambar baru
                Storage::disk('public')->put($filename, (string) $encoded);

                // B. Update array data
                $validated['poster'] = $filename;

            } catch (\Exception $e) {
                report($e);

                // Jika gagal, return error tanpa menghapus gambar lama
                return back()
                    ->withErrors(['poster' => 'Gagal memproses gambar baru. Silakan coba lagi.'])
                    ->withInput();
            }
        }

        $hymneTvri->update($validated);

        if (isset($validated['poster']) && $oldPosterPath) {
            Storage::disk('public')->delete($oldPosterPath);
        }

        return redirect()->route('dashboard.hymne-tvri.index')
            ->with('success', 'Data Himne berhasil diperbarui!');
    }

    /**
     * Menghapus himne.
     */
    public function destroy(HymneTvri $hymneTvri): RedirectResponse
    {
        $posterPath = $hymneTvri->poster;
        $hymneTvri->delete();

        if ($posterPath) {
            Storage::disk('public')->delete($posterPath);
        }

        return redirect()->route('dashboard.hymne-tvri.index')
            ->with('success', 'Data Himne berhasil dihapus!');
    }
}
