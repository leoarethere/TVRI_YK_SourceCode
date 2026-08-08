<?php

namespace App\Http\Controllers;

use App\Models\TugasFungsi;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class DashboardTugasFungsiController extends Controller
{
    public function index(): View
    {
        $tugasFungsi = TugasFungsi::orderBy('type', 'desc')
            ->orderBy('order', 'asc')
            ->paginate(20);

        return view('backend.tugas-fungsi.index', ['tugasFungsi' => $tugasFungsi]);
    }

    public function create(): View
    {
        return view('backend.tugas-fungsi.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:tugas,fungsi',
            'order' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'content' => 'required|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                $filename = 'tf-'.Str::random(20).'.jpg';
                $path = 'task-images/'.$filename;

                $manager = new ImageManager(new Driver);
                $image = $manager->read($file);
                $image->scale(width: 800);
                $encoded = $image->toJpeg(quality: 80);

                Storage::disk('public')->put($path, (string) $encoded);
                $validated['image'] = $path;
            } catch (\Exception $e) {
                report($e);

                return back()->withErrors(['image' => 'Gagal mengunggah gambar. Silakan coba lagi.'])->withInput();
            }
        }

        TugasFungsi::create($validated);

        return redirect()->route('dashboard.tugas-fungsi.index')
            ->with('success', 'Data berhasil ditambahkan!');
    }

    public function edit(TugasFungsi $tugasFungsi): View
    {
        return view('backend.tugas-fungsi.edit', ['tugasFungsi' => $tugasFungsi]);
    }

    public function update(Request $request, TugasFungsi $tugasFungsi): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:tugas,fungsi',
            'order' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'content' => 'required|string',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $oldImagePath = $tugasFungsi->image;

        // [REFACTOR SAFE UPDATE]
        if ($request->hasFile('image')) {
            try {
                // 1. Proses Baru
                $file = $request->file('image');
                $filename = 'tf-'.Str::random(20).'.jpg';
                $path = 'task-images/'.$filename;

                $manager = new ImageManager(new Driver);
                $image = $manager->read($file);
                $image->scale(width: 800);
                $encoded = $image->toJpeg(quality: 80);

                // 2. Simpan
                Storage::disk('public')->put($path, (string) $encoded);

                $validated['image'] = $path;
            } catch (\Exception $e) {
                report($e);

                return back()->withErrors(['image' => 'Gagal mengunggah gambar. Silakan coba lagi.'])->withInput();
            }
        }

        $tugasFungsi->update($validated);

        if (isset($validated['image']) && $oldImagePath) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return redirect()->route('dashboard.tugas-fungsi.index')
            ->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy(TugasFungsi $tugasFungsi): RedirectResponse
    {
        $imagePath = $tugasFungsi->image;
        $tugasFungsi->delete();

        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        return redirect()->route('dashboard.tugas-fungsi.index')
            ->with('success', 'Data berhasil dihapus!');
    }
}
