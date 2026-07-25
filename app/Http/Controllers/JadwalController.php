<?php

namespace App\Http\Controllers;

use App\Models\JadwalAcara;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class JadwalController extends Controller
{
    public function index(Request $request)
    {
        // 1. Join dengan tabel kategori agar bisa sort berdasarkan 'jadwal_categories.order'
        $query = JadwalAcara::select('jadwal_acaras.*') // Pastikan hanya ambil kolom jadwal_acara agar ID tidak tertimpa
                            ->join('jadwal_categories', 'jadwal_acaras.jadwal_category_id', '=', 'jadwal_categories.id')
                            ->with(['jadwalCategory', 'broadcastCategory'])
                            ->where('jadwal_acaras.is_active', true);

        // 2. Filter Pencarian
        if ($request->filled('search')) {
            $query->where('jadwal_acaras.title', 'like', '%' . $request->search . '%');
        }

        // 3. Filter Hari
        if ($request->filled('day')) {
            $query->where('jadwal_categories.slug', $request->day);
        }

        // 4. Sorting: Berdasarkan Order Kategori (1,2,3...), lalu Jam Tayang
        $jadwals = $query->orderBy('jadwal_categories.order', 'asc') // [PERBAIKAN UTAMA]
                        ->orderBy('jadwal_acaras.start_time', 'asc')
                        ->paginate(12)
                        ->withQueryString();

        return view('frontend.publikasi.jadwal', compact('jadwals'));
    }
}