<?php

namespace App\Http\Controllers;

use App\Models\Broadcast;
use App\Models\BroadcastCategory;
use App\Support\LikeSearch;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class BroadcastController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $title = 'Program Siaran';
        $activeCategory = null;

        // Ambil kategori yang punya program 'published' untuk sidebar/filter
        $categories = BroadcastCategory::whereHas('broadcasts', function ($query) {
            $query->publiclyVisible();
        })->orderBy('name')->get();

        // 1. Query Dasar (Hanya yang published)
        $query = Broadcast::with('broadcastCategory')
            ->publiclyVisible();

        // [PERBAIKAN DIMULAI DISINI] ------------------------------
        // 2. Logika Pencarian
        // Cek apakah ada input 'search' dari form
        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search) {
                LikeSearch::contains($q, 'title', $search);
                LikeSearch::contains($q, 'synopsis', $search, 'or');
            });

            // Opsional: Update judul halaman agar user tahu sedang mencari apa
            $title = 'Hasil Pencarian: "'.$search.'"';
        }
        // [PERBAIKAN SELESAI] -------------------------------------

        // 3. Filter berdasarkan Kategori
        if ($request->filled('category')) {
            $activeCategory = $categories->firstWhere('slug', $request->category);

            if ($activeCategory) {
                // Jika sedang mencari + filter kategori, gabungkan judulnya
                if ($request->filled('search')) {
                    $title .= ' di Kategori '.$activeCategory->name;
                } else {
                    $title = 'Penyiaran di Kategori: '.$activeCategory->name;
                }

                $query->where('broadcast_category_id', $activeCategory->id);
            } else {
                // Jika slug kategori tidak valid (diutak-atik di URL), kosongkan hasil
                $query->where('id', -1);
            }
        }

        // 4. Urutkan dan Paginasi
        $broadcasts = $query->latest('published_at')
            ->paginate(12)
            ->withQueryString(); // Penting: agar parameter search tidak hilang saat klik halaman 2

        // Kirim data ke view
        return view('frontend.publikasi.siaran', compact(
            'title',
            'categories',
            'broadcasts',
            'activeCategory'
        ));
    }

    public function show(Broadcast $broadcast): View
    {
        // Pastikan hanya yang published yang bisa diakses detailnya
        if (! $broadcast->isPubliclyVisible()) {
            abort(404);
        }

        return view('frontend.publikasi.detail', [
            'title' => $broadcast->title,
            'broadcast' => $broadcast,
        ]);
    }
}
