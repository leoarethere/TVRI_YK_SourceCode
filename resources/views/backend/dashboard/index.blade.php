<x-dashboard-layout>
    <x-slot:title>
        Dashboard Admin
    </x-slot:title>

    {{-- Header Halaman --}}
    <div class="mb-6 pt-2">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Dashboard Overview</h1>
        <p class="mt-1 text-sm sm:text-base text-gray-500 dark:text-gray-400">
            Selamat datang kembali, {{ auth()->user()->name }}! Berikut ringkasan data website Anda.
        </p>
    </div>

    {{-- GRAFIK PENGUNJUNG (Paling Atas) --}}
    <div x-data="visitorChart()" x-init="init()" class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5 sm:p-6 mb-8">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-lg font-bold text-gray-900 dark:text-white">Statistik Pengunjung</h4>
            <select x-model="filter" @change="fetchData" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-indigo-500 dark:focus:border-indigo-500">
                <option value="today">Hari Ini</option>
                <option value="week">7 Hari Terakhir</option>
                <option value="month">30 Hari Terakhir</option>
                <option value="year">Tahun Ini</option>
            </select>
        </div>
        <div id="visitor-chart-container" class="w-full h-[300px] relative">
            <div x-show="loading" class="absolute inset-0 z-10 flex items-center justify-center bg-white/50 dark:bg-gray-800/50">
                <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <div id="apex-visitor-chart" class="w-full h-full"></div>
        </div>
    </div>

    {{-- GRID KARTU DETAIL DATA WEBSITE --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
        
        {{-- 1. Total Postingan --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center justify-between transition-all hover:shadow-md border-l-4 border-l-indigo-500">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Postingan</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalPosts }}</p>
                <p class="text-xs text-green-600 dark:text-green-400 mt-1 font-medium">+{{ $todayPosts }} hari ini</p>
            </div>
            <div class="flex-shrink-0 p-3 bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400 rounded-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                </svg>
            </div>
        </div>

        {{-- 2. Total Penyiaran (Broadcast) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center justify-between transition-all hover:shadow-md border-l-4 border-l-red-500">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Program Siaran</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalBroadcasts }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Video & Live</p>
            </div>
            <div class="flex-shrink-0 p-3 bg-red-50 text-red-600 dark:bg-red-900/20 dark:text-red-400 rounded-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        {{-- 3. Jadwal Acara --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center justify-between transition-all hover:shadow-md border-l-4 border-l-yellow-500">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Jadwal Acara</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalJadwal }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Agenda Tayang</p>
            </div>
            <div class="flex-shrink-0 p-3 bg-yellow-50 text-yellow-600 dark:bg-yellow-900/20 dark:text-yellow-400 rounded-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        {{-- 4. Info Magang --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center justify-between transition-all hover:shadow-md border-l-4 border-l-blue-500">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Info Magang</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalMagang }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Lowongan aktif</p>
            </div>
            <div class="flex-shrink-0 p-3 bg-blue-50 text-blue-600 dark:bg-blue-900/20 dark:text-blue-400 rounded-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        {{-- 5. Info Kunjungan --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center justify-between transition-all hover:shadow-md border-l-4 border-l-teal-500">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Info Kunjungan</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalKunjungan }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Data tamu</p>
            </div>
            <div class="flex-shrink-0 p-3 bg-teal-50 text-teal-600 dark:bg-teal-900/20 dark:text-teal-400 rounded-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
        </div>

        {{-- 6. Banner Aktif --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center justify-between transition-all hover:shadow-md border-l-4 border-l-pink-500">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Banner Slider</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalBanners }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Gambar beranda</p>
            </div>
            <div class="flex-shrink-0 p-3 bg-pink-50 text-pink-600 dark:bg-pink-900/20 dark:text-pink-400 rounded-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        {{-- 7. Total Pengguna (Digeser ke sini) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center justify-between transition-all hover:shadow-md border-l-4 border-l-gray-500">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Pengguna</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ $totalUsers }}</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Akun admin</p>
            </div>
            <div class="flex-shrink-0 p-3 bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 rounded-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
        </div>
        
        {{-- 8. Total Pengunjung --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center justify-between transition-all hover:shadow-md border-l-4 border-l-emerald-500">
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Total Pengunjung</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalVisitors, 0, ',', '.') }}</p>
                <p class="text-xs text-emerald-600 dark:text-emerald-400 mt-1 font-medium">+{{ number_format($todayVisitors, 0, ',', '.') }} hari ini</p>
            </div>
            <div class="flex-shrink-0 p-3 bg-emerald-50 text-emerald-600 dark:bg-emerald-900/20 dark:text-emerald-400 rounded-lg">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </div>
        </div>

        {{-- 9. Komentar --}}
        <a href="{{ route('dashboard.komentar.index') }}"
           class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-5 flex items-center justify-between transition-all hover:shadow-md border-l-4 border-l-orange-500 group">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400 truncate">Komentar</p>
                    @if($pendingComments > 0)
                        <span class="inline-flex items-center justify-center px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-orange-500 text-white leading-none">
                            {{ $pendingComments }}
                        </span>
                    @endif
                </div>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalComments, 0, ',', '.') }}</p>
                <p class="text-xs mt-1 font-medium
                    {{ $pendingComments > 0
                        ? 'text-orange-500 dark:text-orange-400'
                        : 'text-gray-400 dark:text-gray-500' }}">
                    {{ $pendingComments > 0
                        ? $pendingComments . ' menunggu moderasi'
                        : ($todayComments > 0 ? '+' . $todayComments . ' hari ini' : 'Semua termoderasi') }}
                </p>
            </div>
            <div class="flex-shrink-0 p-3 bg-orange-50 text-orange-600 dark:bg-orange-900/20 dark:text-orange-400 rounded-lg group-hover:scale-110 transition-transform">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
            </div>
        </a>

    </div>

    {{-- AKTIVITAS TERBARU --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-5 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <h4 class="text-lg font-bold text-gray-900 dark:text-white">Pengguna Baru / Aktivitas</h4>
            <a href="{{ route('dashboard.users.index') }}" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                Kelola Pengguna &rarr;
            </a>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-4 py-3">Nama</th>
                        <th scope="col" class="px-4 py-3">Email</th>
                        <th scope="col" class="px-4 py-3">Bergabung</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentActivities as $activity)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white flex items-center gap-3">
                                <img class="w-8 h-8 rounded-full" 
                                     src="https://ui-avatars.com/api/?name={{ urlencode($activity->name) }}&background=random&color=fff" 
                                     alt="{{ $activity->name }}">
                                {{ $activity->name }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $activity->email }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $activity->created_at->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                Belum ada data pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('visitorChart', () => ({
                filter: 'week',
                loading: false,
                chart: null,
                init() {
                    // Coba render chart jika ApexCharts sudah tersedia
                    this.checkAndRender();
                },
                checkAndRender() {
                    if (window.ApexCharts) {
                        this.renderChart();
                        this.fetchData();
                    } else {
                        // Jika belum ter-load (Vite async load), tunggu sebentar
                        setTimeout(() => this.checkAndRender(), 100);
                    }
                },
                renderChart() {
                    const options = {
                        series: [{
                            name: 'Pengunjung',
                            data: []
                        }],
                        chart: {
                            type: 'area',
                            height: 300,
                            fontFamily: 'Inter, sans-serif',
                            toolbar: { show: false },
                            zoom: { enabled: false }
                        },
                        colors: ['#4F46E5'], // Indigo 600
                        fill: {
                            type: 'gradient',
                            gradient: {
                                shadeIntensity: 1,
                                opacityFrom: 0.7,
                                opacityTo: 0.1,
                                stops: [0, 90, 100]
                            }
                        },
                        dataLabels: { enabled: false },
                        stroke: { curve: 'smooth', width: 2 },
                        xaxis: {
                            categories: [],
                            axisBorder: { show: false },
                            axisTicks: { show: false },
                            labels: {
                                style: {
                                    colors: '#9CA3AF',
                                    fontSize: '12px'
                                }
                            }
                        },
                        yaxis: {
                            labels: {
                                style: {
                                    colors: '#9CA3AF',
                                    fontSize: '12px'
                                },
                                formatter: function(val) { return Math.round(val); }
                            }
                        },
                        grid: {
                            borderColor: '#E5E7EB',
                            strokeDashArray: 4,
                            yaxis: { lines: { show: true } },
                            xaxis: { lines: { show: false } },
                            padding: { top: 0, right: 0, bottom: 0, left: 10 }
                        },
                        theme: {
                            mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light'
                        },
                        tooltip: {
                            theme: document.documentElement.classList.contains('dark') ? 'dark' : 'light',
                            y: { formatter: function(val) { return val + " hits" } }
                        }
                    };

                    this.chart = new window.ApexCharts(document.querySelector("#apex-visitor-chart"), options);
                    this.chart.render();

                    // Observer untuk theme dark/light mode toggle
                    const observer = new MutationObserver((mutations) => {
                        mutations.forEach((mutation) => {
                            if (mutation.attributeName === 'class') {
                                const isDark = document.documentElement.classList.contains('dark');
                                this.chart.updateOptions({
                                    theme: { mode: isDark ? 'dark' : 'light' },
                                    tooltip: { theme: isDark ? 'dark' : 'light' },
                                    grid: { borderColor: isDark ? '#374151' : '#E5E7EB' }
                                });
                            }
                        });
                    });
                    observer.observe(document.documentElement, { attributes: true });
                },
                fetchData() {
                    this.loading = true;
                    fetch(`/dashboard/visitor-chart?filter=${this.filter}`)
                        .then(res => res.json())
                        .then(data => {
                            if (this.chart) {
                                this.chart.updateSeries([{
                                    name: 'Pengunjung',
                                    data: data.series
                                }]);
                                this.chart.updateOptions({
                                    xaxis: { categories: data.labels }
                                });
                            }
                        })
                        .catch(err => console.error("Error fetching chart data:", err))
                        .finally(() => {
                            this.loading = false;
                        });
                }
            }));
        });
    </script>
</x-dashboard-layout>