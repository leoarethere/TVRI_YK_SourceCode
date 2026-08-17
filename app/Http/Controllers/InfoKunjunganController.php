<?php

namespace App\Http\Controllers;

use App\Models\InfoKunjungan;
use App\Models\InfoKunjunganFaq;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class InfoKunjunganController extends Controller
{
    /**
     * Menampilkan halaman publik informasi kunjungan.
     */
    public function index(Request $request): View
    {
        // Ambil data yang aktif saja
        $query = InfoKunjungan::where('is_active', true);

        // 1. Filter Pencarian
        if ($request->filled('search')) {
            $query->where('title', 'like', '%'.$request->search.'%');
        }

        // 2. Logika Sorting (Perbaikan disini)
        switch ($request->input('sort')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;
            default:
                $query->latest();
                break;
        }

        // 3. Eksekusi Pagination
        $items = $query->paginate(10)->withQueryString();

        // [BARU] Ambil Data FAQ
        $faqs = InfoKunjunganFaq::where('is_active', true)
            ->orderBy('order', 'asc')
            ->get();

        // [UPDATE] Kirim $faqs ke view
        return view('frontend.info-kunjungan.index', compact('items', 'faqs'));
    }
}
