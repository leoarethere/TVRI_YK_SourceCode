<?php

namespace App\Http\Controllers;

use App\Models\VisiMisi;
use App\Support\PublicStorageWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class DashboardVisiMisiController extends Controller
{
    public function index(): View
    {
        // [PERBAIKAN] Tambah pengecekan jika data kosong
        $visiMisis = VisiMisi::orderBy('type', 'desc')
            ->orderBy('order', 'asc')
            ->paginate(10);

        // [PERBAIKAN] Pastikan data dikirim dengan penanda khusus jika kosong
        $isEmpty = $visiMisis->isEmpty();

        return view('backend.visi-misi.index', compact('visiMisis', 'isEmpty'));
    }

    public function create(): View
    {
        return view('backend.visi-misi.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:visi,misi',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'order' => 'required|integer|min:0',
            'is_active' => 'required|in:0,1',
        ]);

        // Logika Upload & Kompresi Gambar
        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                $filename = Str::random(40).'.jpg';
                $path = 'visi-misi-images/'.$filename;

                $manager = new ImageManager(new Driver);
                $image = $manager->read($file);
                $image->scale(width: 800);
                $encodedImage = $image->toJpeg(quality: 70);

                PublicStorageWriter::put($path, (string) $encodedImage);
                $validated['image'] = $path;

            } catch (\Exception $e) {
                report($e);

                return back()->withErrors([
                    'image' => 'Gagal memproses gambar. Silakan coba lagi.',
                ])->withInput();
            }
        }

        VisiMisi::create($validated);

        return redirect()->route('dashboard.visi-misi.index')
            ->with('success', 'Data Visi/Misi berhasil ditambahkan!');
    }

    public function edit(VisiMisi $visiMisi): View
    {
        return view('backend.visi-misi.edit', compact('visiMisi'));
    }

    public function update(Request $request, VisiMisi $visiMisi): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:visi,misi',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'order' => 'required|integer|min:0',
            'is_active' => 'required|in:0,1',
        ]);

        $oldImagePath = $visiMisi->image;

        // Logika Update Gambar (Safe Order: simpan baru → hapus lama)
        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                $filename = Str::random(40).'.jpg';
                $path = 'visi-misi-images/'.$filename;

                $manager = new ImageManager(new Driver);
                $image = $manager->read($file);
                $image->scale(width: 800);
                $encodedImage = $image->toJpeg(quality: 70);

                // 1. Simpan gambar baru terlebih dahulu
                PublicStorageWriter::put($path, (string) $encodedImage);

                $validated['image'] = $path;

            } catch (\Exception $e) {
                report($e);

                return back()->withErrors([
                    'image' => 'Gagal memproses gambar. Silakan coba lagi.',
                ])->withInput();
            }
        }

        $visiMisi->update($validated);

        if (isset($validated['image']) && $oldImagePath) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return redirect()->route('dashboard.visi-misi.index')
            ->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(VisiMisi $visiMisi): RedirectResponse
    {
        $imagePath = $visiMisi->image;
        $visiMisi->delete();

        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        return redirect()->route('dashboard.visi-misi.index')
            ->with('success', 'Data berhasil dihapus!');
    }
}
