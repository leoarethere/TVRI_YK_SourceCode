<?php

namespace App\Http\Controllers;

use App\Models\Prestasi;
use App\Support\PublicStorageWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class DashboardPrestasiController extends Controller
{
    public function index(Request $request): View
    {
        // [PERBAIKAN LOGIKA] Menambahkan fitur Search & Filter
        $query = Prestasi::query();

        // 1. Logika Pencarian (Search)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%'.$search.'%')
                    ->orWhere('award_name', 'like', '%'.$search.'%');
            });
        }

        // 2. Logika Filter Kategori
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // 3. Sorting Default (Tahun Terbaru)
        // Kita gunakan withQueryString() agar parameter search/category tidak hilang saat pindah halaman
        $prestasis = $query->orderBy('year', 'desc')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('backend.prestasi.index', compact('prestasis'));
    }

    public function create(): View
    {
        return view('backend.prestasi.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'award_name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'category' => 'required|string|max:100',
            'year' => 'required|digits:4|integer|min:1900|max:'.(date('Y') + 1),
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            // 'is_active' tidak perlu divalidasi disini karena diambil manual
        ]);

        // Proses Upload Gambar
        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                $filename = 'prestasi/'.Str::random(40).'.jpg';

                $manager = new ImageManager(new Driver);
                $image = $manager->read($file);
                $image->scale(width: 800);
                $encoded = $image->toJpeg(quality: 80);

                PublicStorageWriter::put($filename, (string) $encoded);
                $validated['image'] = $filename;

            } catch (\Exception $e) {
                report($e);

                return back()->withErrors(['image' => 'Gagal memproses gambar. Silakan coba lagi.'])->withInput();
            }
        }

        // Logika Checkbox: Jika dicentang return true (1), jika tidak ada return false (0)
        $validated['is_active'] = $request->has('is_active');

        Prestasi::create($validated);

        return redirect()->route('dashboard.prestasi.index')
            ->with('success', 'Data Prestasi berhasil ditambahkan!');
    }

    public function edit(Prestasi $prestasi): View
    {
        return view('backend.prestasi.edit', compact('prestasi'));
    }

    public function update(Request $request, Prestasi $prestasi): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'award_name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'category' => 'required|string|max:100',
            'year' => 'required|digits:4|integer|min:1900|max:'.(date('Y') + 1),
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        // [PERBAIKAN LOGIKA] Ambil status aktif
        $validated['is_active'] = $request->has('is_active');

        $oldImagePath = $prestasi->image;

        // [PERBAIKAN LOGIKA] Handle Update Image Lebih Aman
        // Kita proses upload dulu, baru hapus yang lama jika upload sukses.
        // Sebelumnya: Hapus dulu -> Upload (Kalau upload gagal, gambar lama hilang tapi DB masih nyatat nama file lama)
        if ($request->hasFile('image')) {
            try {
                $file = $request->file('image');
                $filename = 'prestasi/'.Str::random(40).'.jpg';

                $manager = new ImageManager(new Driver);
                $image = $manager->read($file);
                $image->scale(width: 800);
                $encoded = $image->toJpeg(quality: 80);

                // 1. Simpan gambar baru
                PublicStorageWriter::put($filename, (string) $encoded);

                // 2. Masukkan nama file baru ke data validasi
                $validated['image'] = $filename;

            } catch (\Exception $e) {
                report($e);

                return back()->withErrors(['image' => 'Gagal memproses gambar. Silakan coba lagi.'])->withInput();
            }
        }

        $prestasi->update($validated);

        if (isset($validated['image']) && $oldImagePath) {
            Storage::disk('public')->delete($oldImagePath);
        }

        return redirect()->route('dashboard.prestasi.index')
            ->with('success', 'Data Prestasi berhasil diperbarui!');
    }

    public function destroy(Prestasi $prestasi): RedirectResponse
    {
        $imagePath = $prestasi->image;
        $prestasi->delete();

        if ($imagePath) {
            Storage::disk('public')->delete($imagePath);
        }

        return redirect()->route('dashboard.prestasi.index')
            ->with('success', 'Data Prestasi berhasil dihapus!');
    }
}
