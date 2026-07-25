<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Routing\Controller;

class StreamingController extends Controller
{
    /**
     * Menampilkan halaman Live Streaming TVRI D.I. Yogyakarta.
     */
    public function index(): View
    {
        $stream = (object) [
            'title' => 'Live Streaming TVRI D.I. Yogyakarta',
            'description' => 'Saksikan siaran langsung berita, budaya, dan hiburan khas Jogja dengan kualitas HD.',
            'stream_url' => config('services.tvri.stream_url'),
            'poster' => asset('img/loginbanner.jpeg'), 
            
            'is_live' => true,
        ];

        return view('frontend.streaming.streaming', [
            'title' => 'Live Streaming',
            'stream' => $stream
        ]);
    }
}