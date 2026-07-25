<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use App\Models\Visitor;
use Illuminate\Http\Request;
use App\Models\Banner;
use App\Models\Broadcast;
use App\Models\InfoMagang;
use App\Models\JadwalAcara;
use App\Models\InfoKunjungan;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Mengambil Statistik Data untuk Grid Kartu
        // withoutEagerLoads() mencegah eager loading relasi yang tidak perlu untuk count()
        $stats = [
            'totalPosts'      => Post::withoutEagerLoads()->count(),
            'totalBroadcasts' => Broadcast::withoutEagerLoads()->count(),
            'totalJadwal'     => JadwalAcara::withoutEagerLoads()->count(),
            'totalMagang'     => InfoMagang::count(),
            'totalKunjungan'  => InfoKunjungan::count(),
            'totalBanners'    => Banner::count(),
            'totalUsers'      => User::count(),
            'totalVisitors'   => Visitor::sum('hits'),
            'totalComments'   => Comment::count(),
            'pendingComments' => Comment::where('status', 'pending')->count(),

            // Statistik Tambahan
            'todayPosts'      => Post::withoutEagerLoads()->whereDate('created_at', today())->count(),
            'todayVisitors'   => Visitor::whereDate('date', today())->sum('hits'),
            'todayComments'   => Comment::whereDate('created_at', today())->count(),
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
    public function visitorChartData(Request $request)
    {
        $filter = $request->query('filter', 'week'); // default: week
        $query = Visitor::query();

        $labels = [];
        $series = [];

        if ($filter === 'today') {
            // Data for today (e.g. grouped by hours if we had time, but we only have date and hits per date)
            // Since we only track hits per date, 'today' will just return 1 point
            $data = $query->whereDate('date', today())->first();
            $labels[] = today()->translatedFormat('d M Y');
            $series[] = $data ? $data->hits : 0;
        } elseif ($filter === 'week') {
            // Last 7 days
            $startDate = today()->subDays(6);
            $endDate = today();
            $visitors = $query->whereBetween('date', [$startDate, $endDate])->orderBy('date', 'asc')->get()->keyBy('date');
            
            for ($i = 0; $i < 7; $i++) {
                $date = $startDate->copy()->addDays($i)->format('Y-m-d');
                $labels[] = \Carbon\Carbon::parse($date)->translatedFormat('d M');
                $series[] = isset($visitors[$date]) ? $visitors[$date]->hits : 0;
            }
        } elseif ($filter === 'month') {
            // Last 30 days
            $startDate = today()->subDays(29);
            $endDate = today();
            $visitors = $query->whereBetween('date', [$startDate, $endDate])->orderBy('date', 'asc')->get()->keyBy('date');
            
            for ($i = 0; $i < 30; $i++) {
                $date = $startDate->copy()->addDays($i)->format('Y-m-d');
                // Format label differently maybe just every few days if needed, but ApexCharts handles long labels well usually.
                $labels[] = \Carbon\Carbon::parse($date)->translatedFormat('d M');
                $series[] = isset($visitors[$date]) ? $visitors[$date]->hits : 0;
            }
        } elseif ($filter === 'year') {
            // Last 12 months. We need to group by month.
            // Since sqlite or mysql might have different group by, we can just fetch all for the year and group in collection.
            $startDate = today()->startOfYear();
            $endDate = today()->endOfYear();
            $visitors = $query->whereBetween('date', [$startDate, $endDate])->get();
            
            $grouped = $visitors->groupBy(function($item) {
                return \Carbon\Carbon::parse($item->date)->format('Y-m'); // e.g. 2024-01
            });

            for ($i = 1; $i <= 12; $i++) {
                $monthStr = today()->startOfYear()->addMonths($i - 1)->format('Y-m');
                $labels[] = today()->startOfYear()->addMonths($i - 1)->translatedFormat('M');
                $hits = isset($grouped[$monthStr]) ? $grouped[$monthStr]->sum('hits') : 0;
                $series[] = $hits;
            }
        }

        return response()->json([
            'labels' => $labels,
            'series' => $series,
        ]);
    }
}