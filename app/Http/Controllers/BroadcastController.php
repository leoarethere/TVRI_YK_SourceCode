<?php

namespace App\Http\Controllers;

use App\Models\Broadcast;
use Illuminate\Http\Request;
use App\Models\BroadcastCategory;
use Illuminate\Routing\Controller;

class BroadcastController extends Controller
{
    public function index(Request $request)
    {
        $title = 'Program Siaran';
        $activeCategory = null; 
        
        // Ambil kategori yang punya program 'published' untuk sidebar/filter
        $categories = BroadcastCategory::whereHas('broadcasts', function ($query) {
            $query->where('status', 'published');
        })->orderBy('name')->get();

        // 1. Query Dasar (Hanya yang published)
        $query = Broadcast::with('broadcastCategory')
                          ->where('status', 'published');

        // [PERBAIKAN DIMULAI DISINI] ------------------------------
        // 2. Logika Pencarian
        // Cek apakah ada input 'search' dari form
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('synopsis', 'like', '%' . $search . '%');
            });
            
            // Opsional: Update judul halaman agar user tahu sedang mencari apa
            $title = 'Hasil Pencarian: "' . $search . '"';
        }
        // [PERBAIKAN SELESAI] -------------------------------------

        // 3. Filter berdasarkan Kategori
        if ($request->filled('category')) {
            $activeCategory = $categories->firstWhere('slug', $request->category);
            
            if ($activeCategory) {
                // Jika sedang mencari + filter kategori, gabungkan judulnya
                if ($request->filled('search')) {
                    $title .= ' di Kategori ' . $activeCategory->name;
                } else {
                    $title = 'Penyiaran di Kategori: ' . $activeCategory->name;
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

    public function show(Broadcast $broadcast)
    {
        // Pastikan hanya yang published yang bisa diakses detailnya
        if ($broadcast->status !== 'published') {
            abort(404);
        }

        return view('frontend.publikasi.detail', [
            'title' => $broadcast->title,
            'broadcast' => $broadcast
        ]);
    }
}
