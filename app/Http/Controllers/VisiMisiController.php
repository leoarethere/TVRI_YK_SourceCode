<?php

namespace App\Http\Controllers;

use App\Models\VisiMisi;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class VisiMisiController extends Controller
{
    public function visiMisi(): View
    {
        $title = 'Visi dan Misi';

        // [PERBAIKAN] Mengambil data sebagai Collection (get), bukan single object (first)
        // dan menamakannya $visis (jamak)
        $visis = VisiMisi::where('type', 'visi')
            ->where('is_active', true)
            ->orderBy('order', 'asc')
            ->get();

        // Ambil semua Misi
        $misis = VisiMisi::where('type', 'misi')
            ->where('is_active', true)
            ->orderBy('order', 'asc')
            ->get();

        // Kirim $visis dan $misis ke view
        return view('frontend.tentang.visi-misi', compact('title', 'visis', 'misis'));
    }
}
