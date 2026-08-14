<?php

namespace App\Http\Controllers;

use App\Models\AksesInformasi;
use App\Models\Banner;
use App\Models\Broadcast;
use App\Models\Category;
use App\Models\ContactInfo;
use App\Models\JadwalAcara;
use App\Models\JadwalCategory;
use App\Models\Post;
use App\Models\User;
use App\Support\HomepageCache;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
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
        $data = Cache::remember(HomepageCache::DATA_KEY, 600, function () {

            // A. Postingan untuk Slider (Hero Posts)
            $heroPosts = Post::with(['user', 'category'])
                ->publiclyVisible()
                ->whereNotNull('featured_image') // Wajib punya gambar
                ->latest('published_at')
                ->take(5)
                ->get();

            // B. Postingan Terbaru (List/Grid)
            $latestPosts = Post::with(['user', 'category'])
                ->publiclyVisible()
                ->latest('published_at')
                ->take(6) // Ambil 6 agar grid rapi (2 baris x 3 kolom)
                ->get();

            // C. Program Unggulan (Random/Latest Broadcasts)
            $featuredBroadcasts = Broadcast::with('broadcastCategory')
                ->publiclyVisible()
                ->where('is_active', true)
                ->inRandomOrder() // Acak agar variatif setiap refresh cache
                ->take(8)
                ->get();

            // E. Statistik & Kategori
            $categories = Category::whereHas(
                'posts',
                fn ($query) => $query->publiclyVisible()
            )
                ->withCount([
                    'posts' => fn ($query) => $query->publiclyVisible(),
                ])
                ->orderByDesc('posts_count')
                ->take(8)
                ->get();

            $stats = [
                'posts' => Post::publiclyVisible()->count(),
                'categories' => Category::count(),
                'users' => User::count(),
                'programs' => Broadcast::publiclyVisible()->count(),
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
            HomepageCache::scheduleKey(),
            600,
            function () use ($todaySlug) {
                $categoryIds = JadwalCategory::query()
                    ->get(['id', 'slug'])
                    ->filter(fn (JadwalCategory $category) => $category->appliesToDay($todaySlug))
                    ->pluck('id');

                return JadwalAcara::with('broadcastCategory')
                    ->whereIn('jadwal_category_id', $categoryIds)
                    ->where('is_active', true)
                    ->orderBy('start_time')
                    ->get();
            }
        );

        $currentProgram = $todaySchedules->first(function (JadwalAcara $item) use ($now) {
            $start = $now->copy()->setTime(
                $item->start_time->hour,
                $item->start_time->minute,
            );
            $end = $item->end_time === null
                ? $start->copy()->addHour()
                : $now->copy()->setTime($item->end_time->hour, $item->end_time->minute);

            if ($end->lessThanOrEqualTo($start)) {
                $end->addDay();
            }

            return $now->greaterThanOrEqualTo($start) && $now->lessThan($end);
        });
        $nextProgram = $todaySchedules->first(
            fn (JadwalAcara $item) => $now->copy()->setTime(
                $item->start_time->hour,
                $item->start_time->minute,
            )->greaterThan($now)
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
