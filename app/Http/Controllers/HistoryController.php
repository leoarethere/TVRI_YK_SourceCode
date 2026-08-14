<?php

namespace App\Http\Controllers;

use App\Models\History;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class HistoryController extends Controller
{
    /**
     * Menampilkan halaman Sejarah publik.
     */
    public function index(): View
    {
        // Mengambil data history yang sudah dipublikasikan, diurutkan kronologis
        $histories = History::publiclyVisible()->oldest()->get();

        return view('frontend.tentang.sejarah', [
            'title' => 'Sejarah TVRI Stasiun D.I. Yogyakarta',
            'histories' => $histories,
        ]);
    }
}
