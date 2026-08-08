<?php

namespace App\Http\Controllers;

use App\Models\AksesInformasi;
use App\Models\Banner;
use App\Models\Broadcast;
use App\Models\Category;
use App\Models\ContactInfo;
use App\Models\JadwalAcara;
use App\Models\Post;
use App\Models\User;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class HomeController extends Controller
{
    public function index()
    {
        // 1. Banner Slider (Cache 1 Jam)
        $heroSlides = Cache::remember('homepage.slides', 3600, function () {
            return Banner::where('is_active', true)
                ->orderBy('order')
                ->get()
                ->map(fn ($banner) => [
                    'image' => Storage::url($banner->image_path),
                    'title' => $banner->title,
                    'subtitle' => $banner->subtitle,
                    'link' => $banner->link,
                    'button_text' => $banner->button_text,
                ])
                ->toArray();
        });

        // 2. Data Dinamis (Postingan, Jadwal, Program) - Cache lebih singkat (misal 5-10 menit)
        $data = Cache::remember('homepage.data.v2', 600, function () {

            // A. Postingan untuk Slider (Hero Posts)
            $heroPosts = Post::with(['user', 'category'])
                ->where('status', 'published')
                ->whereNotNull('featured_image') // Wajib punya gambar
                ->latest('published_at')
                ->take(5)
                ->get();

            // B. Postingan Terbaru (List/Grid)
            $latestPosts = Post::with(['user', 'category'])
                ->where('status', 'published')
                ->latest('published_at')
                ->take(6) // Ambil 6 agar grid rapi (2 baris x 3 kolom)
                ->get();

            // C. Program Unggulan (Random/Latest Broadcasts)
            $featuredBroadcasts = Broadcast::with('broadcastCategory')
                ->where('status', 'published')
                ->where('is_active', true)
                ->inRandomOrder() // Acak agar variatif setiap refresh cache
                ->take(8)
                ->get();

            // E. Statistik & Kategori
            $categories = Category::whereHas(
                'posts',
                fn ($query) => $query->where('status', 'published')
            )
                ->withCount([
                    'posts' => fn ($query) => $query->where('status', 'published'),
                ])
                ->orderByDesc('posts_count')
                ->take(8)
                ->get();

            $stats = [
                'posts' => Post::where('status', 'published')->count(),
                'categories' => Category::count(),
                'users' => User::count(),
                'programs' => Broadcast::where('status', 'published')->count(),
            ];

            $contactInfo = ContactInfo::where('is_active', true)->first();

            return compact(
                'heroPosts',
                'latestPosts',
                'featuredBroadcasts',
                'categories',
                'stats',
                'contactInfo'
            );
        });

        $now = now();
        $todaySlug = strtolower($now->copy()->locale('id')->isoFormat('dddd'));
        $todaySchedules = Cache::remember(
            'homepage.schedules.'.$now->toDateString(),
            600,
            fn () => JadwalAcara::with('broadcastCategory')
                ->whereHas('jadwalCategory', fn ($query) => $query->where('slug', $todaySlug))
                ->where('is_active', true)
                ->orderBy('start_time')
                ->get()
        );

        $currentTime = $now->format('H:i');
        $currentProgram = $todaySchedules->first(function ($item) use ($currentTime) {
            $end = $item->end_time?->format('H:i') ?? '23:59';

            return $item->start_time->format('H:i') <= $currentTime && $end > $currentTime;
        });
        $nextProgram = $todaySchedules->first(
            fn ($item) => $item->start_time->format('H:i') > $currentTime
        );

        // Menggabungkan semua data untuk dikirim ke View
        // AksesInformasi TIDAK di-cache agar perubahan langsung terlihat
        $aksesInternal = AksesInformasi::internal();
        $aksesPublik = AksesInformasi::publik();

        return view('frontend.beranda.index', array_merge([
            'title' => 'Beranda - TVRI D.I. Yogyakarta',
            'heroSlides' => $heroSlides,
            'aksesInternal' => $aksesInternal,
            'aksesPublik' => $aksesPublik,
            'todaySchedules' => $todaySchedules,
            'currentProgram' => $currentProgram,
            'nextProgram' => $nextProgram,
        ], $data));
    }
}
