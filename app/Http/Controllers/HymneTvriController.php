<?php

namespace App\Http\Controllers;

use App\Models\HymneTvri;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class HymneTvriController extends Controller
{
    public function index(): View
    {
        // Only show active hymne records to public visitors
        $hymnes = HymneTvri::where('is_active', true)->latest()->paginate(5);

        return view('frontend.tentang.hymne-tvri', compact('hymnes'));
    }
}
