<?php

namespace App\Http\Controllers;

use App\Models\ReformasiRb;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class ReformasiRbController extends Controller
{
    public function index(Request $request): View
    {
        // Query dasar: hanya yang aktif
        $query = ReformasiRb::where('is_active', true);

        // 1. Filter Pencarian
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // 2. Logika Sorting (Perbaikan disini)
        switch ($request->input('sort')) {
            case 'oldest':
                $query->orderBy('created_at', 'asc'); // Terlama
                break;
            case 'title_asc':
                $query->orderBy('title', 'asc'); // Judul A-Z
                break;
            case 'title_desc':
                $query->orderBy('title', 'desc'); // Judul Z-A
                break;
            default:
                $query->latest(); // Default: Terbaru (created_at desc)
                break;
        }

        // 3. Eksekusi Pagination
        $items = $query->paginate(10)->withQueryString();

        return view('frontend.reformasi-rb.index', compact('items'));
    }
}
