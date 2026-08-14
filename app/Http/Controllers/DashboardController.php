<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use App\Models\Broadcast;
use App\Models\Comment;
use App\Models\InfoKunjungan;
use App\Models\InfoMagang;
use App\Models\JadwalAcara;
use App\Models\Post;
use App\Models\User;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // 1. Mengambil Statistik Data untuk Grid Kartu
        // withoutEagerLoads() mencegah eager loading relasi yang tidak perlu untuk count()
        $stats = [
            'totalPosts' => Post::withoutEagerLoads()->count(),
            'totalBroadcasts' => Broadcast::withoutEagerLoads()->count(),
            'totalJadwal' => JadwalAcara::withoutEagerLoads()->count(),
            'totalMagang' => InfoMagang::count(),
            'totalKunjungan' => InfoKunjungan::count(),
            'totalBanners' => Banner::count(),
            'totalUsers' => User::count(),
            'totalVisitors' => Visitor::sum('hits'),
            'totalComments' => Comment::count(),
            'pendingComments' => Comment::where('status', 'pending')->count(),

            // Statistik Tambahan
            'todayPosts' => Post::withoutEagerLoads()->whereDate('created_at', today())->count(),
            'todayVisitors' => Visitor::whereDate('date', today())->sum('hits'),
            'todayComments' => Comment::whereDate('created_at', today())->count(),
        ];

        // 2. Mengambil Aktivitas Terbaru (5 User Terakhir)
        $recentActivities = User::latest()->take(5)->get();

        // 3. Mengirim semua data ke View
        return view('backend.dashboard.index', array_merge($stats, [
            'recentActivities' => $recentActivities,
        ]));
    }

    /**
     * Get data for visitor chart based on the selected filter.
     */
    public function visitorChartData(Request $request): JsonResponse
    {
        $filter = $request->query('filter', 'week');

        // Validasi: hanya izinkan filter yang dikenali
        if (! in_array($filter, ['today', 'week', 'month', 'year'])) {
            $filter = 'week';
        }

        $labels = [];
        $series = [];

        if ($filter === 'today') {
            $dailyHits = $this->dailyHits(today(), today());
            $labels[] = today()->translatedFormat('d M Y');
            $series[] = $dailyHits->get(today()->toDateString(), 0);
        } elseif ($filter === 'week') {
            $startDate = today()->subDays(6);
            $endDate = today();
            $dailyHits = $this->dailyHits($startDate, $endDate);

            for ($i = 0; $i < 7; $i++) {
                $date = $startDate->copy()->addDays($i)->format('Y-m-d');
                $labels[] = Carbon::parse($date)->translatedFormat('d M');
                $series[] = $dailyHits->get($date, 0);
            }
        } elseif ($filter === 'month') {
            $startDate = today()->subDays(29);
            $endDate = today();
            $dailyHits = $this->dailyHits($startDate, $endDate);

            for ($i = 0; $i < 30; $i++) {
                $date = $startDate->copy()->addDays($i)->format('Y-m-d');
                $labels[] = Carbon::parse($date)->translatedFormat('d M');
                $series[] = $dailyHits->get($date, 0);
            }
        } elseif ($filter === 'year') {
            $startDate = today()->startOfYear();
            $endDate = today()->endOfYear();
            $monthlyHits = $this->dailyHits($startDate, $endDate)
                ->groupBy(fn ($hits, $date) => substr($date, 0, 7))
                ->map->sum();

            for ($i = 1; $i <= 12; $i++) {
                $monthStr = today()->startOfYear()->addMonths($i - 1)->format('Y-m');
                $labels[] = today()->startOfYear()->addMonths($i - 1)->translatedFormat('M');
                $series[] = $monthlyHits->get($monthStr, 0);
            }
        }

        return response()->json([
            'labels' => $labels,
            'series' => $series,
        ]);
    }

    private function dailyHits(Carbon $startDate, Carbon $endDate): Collection
    {
        return Visitor::query()
            ->where('date', '>=', $startDate->toDateString())
            ->where('date', '<', $endDate->copy()->addDay()->toDateString())
            ->selectRaw('DATE(date) as visit_date, SUM(hits) as total_hits')
            ->groupByRaw('DATE(date)')
            ->orderByRaw('DATE(date)')
            ->pluck('total_hits', 'visit_date')
            ->map(fn ($hits) => (int) $hits);
    }
}
