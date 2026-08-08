<x-layout>
    <x-slot:title>{{ $title ?? 'Beranda' }}</x-slot:title>

    {{-- ========================================== --}}
    {{-- 1. HERO CAROUSEL (BANNER UTAMA)            --}}
    {{-- ========================================== --}}
    @if(isset($heroSlides) && count($heroSlides) > 0)
        <div class="py-2 px-4 sm:px-6 lg:px-8">
            <div class="mx-auto">
                <x-hero-carousel :slides="$heroSlides" />
            </div>
        </div>
    @endif

    {{-- ========================================== --}}
    {{-- 2. HERO SECTION — LAYANAN SATU DATA        --}}
    {{-- ========================================== --}}
    <section class="py-6 px-4 sm:px-6 lg:px-8">
        <div class="mx-auto">
            <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 dark:from-blue-900 dark:via-blue-950 dark:to-indigo-950 shadow-xl">
                {{-- Background pattern --}}
                <div class="absolute inset-0 bg-grid-white/[0.05] bg-[size:24px_24px]"></div>
                <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>

                <div class="relative z-10 px-6 py-12 lg:px-16 lg:py-16">
                    <div class="max-w-4xl mx-auto text-center">

                        {{-- Judul --}}
                        <h2 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white mb-4 drop-shadow-sm leading-tight">
                            Layanan Satu Data TVRI
                        </h2>

                        {{-- Deskripsi --}}
                        <p class="text-blue-100 text-sm sm:text-base max-w-2xl mx-auto leading-relaxed mb-8 opacity-90">
                            Layanan Satu Data TVRI adalah sistem keterbukaan informasi publik yang dikelola oleh PPID TVRI untuk menyediakan akses transparan, terintegrasi, dan mudah terhadap dokumen serta data publik TVRI, termasuk di tingkat stasiun daerah seperti TVRI D.I. Yogyakarta.
                        </p>

                        {{-- Tombol Aksi --}}
                        <div class="flex flex-wrap justify-center gap-4">

                            {{-- Tombol Internal — mengarah ke portal login pegawai --}}
                            @if($aksesInternal->is_active)
                            <a href="{{ route('pegawai.login') }}"
                               class="group inline-flex items-center gap-2 px-6 py-3 text-sm font-bold text-blue-900 bg-white rounded-xl hover:bg-blue-50 transition-colors shadow-lg">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                {{ $aksesInternal->label ?: 'Akses Informasi Internal' }}
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                            @endif

                            {{-- Tombol Publik (Outline Putih — konsisten dengan section Visi & Misi) --}}
                            @if($aksesPublik->is_active)
                            <a href="{{ $aksesPublik->url ?: route('ppid.index') }}"
                               {{ $aksesPublik->url ? 'target="_blank" rel="noopener noreferrer"' : '' }}
                               class="group inline-flex items-center gap-2 px-6 py-3 text-sm font-bold text-white bg-transparent border-2 border-white/30 rounded-xl hover:bg-white/10 transition-colors">
                                <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 004 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $aksesPublik->label ?: 'Akses Informasi Publik' }}
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================================== --}}
    {{-- 3. PROGRAM ACARA (SLIDER)                  --}}
    {{-- ========================================== --}}
    @if(isset($featuredBroadcasts) && $featuredBroadcasts->isNotEmpty())
    <section class="py-8 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
                
            {{-- Header Section --}}
            <div class="flex items-end justify-between mb-8">
                <div>
                    <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white mt-1">Program Unggulan</h2>
                </div>
                <a href="{{ route('penyiaran.index') }}" class="group flex items-center text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 transition-colors">
                    Lihat Semua
                    <svg class="w-4 h-4 ml-1 transition-transform group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                </a>
            </div>

            {{-- Slider Container --}}
            <div class="relative group">
                {{-- Scroll Container --}}
                <div class="flex overflow-x-auto gap-4 sm:gap-6 pb-8 snap-x snap-mandatory scrollbar-hide -mx-4 px-4 sm:mx-0 sm:px-0">
                    @foreach($featuredBroadcasts as $program)
                        <a href="{{ route('penyiaran.show', $program) }}" 
                           class="snap-center shrink-0 w-[180px] sm:w-[220px] group/card block bg-white dark:bg-gray-800 rounded-2xl overflow-hidden border border-gray-200 dark:border-gray-700 shadow-sm hover:shadow-xl transition-all duration-300 hover:border-blue-300 dark:hover:border-blue-700 hover:ring-2 hover:ring-blue-500/20">
                            
                            {{-- Poster Container dengan Aspect Ratio 3:4 --}}
                            <div class="relative aspect-[3/4] overflow-hidden bg-gray-100 dark:bg-gray-900">
                                @if($program->poster)
                                    <img src="{{ Storage::url($program->poster) }}" 
                                         alt="{{ $program->title }}" 
                                         class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover/card:scale-105" 
                                         loading="lazy">
                                    
                                    {{-- Overlay Hover --}}
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover/card:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                                        <div class="transform translate-y-4 group-hover/card:translate-y-0 transition-transform duration-300">
                                            <span class="inline-flex items-center gap-2 text-white text-xs font-bold uppercase tracking-wider">
                                                <span>Lihat Detail</span>
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                                </svg>
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    {{-- Placeholder --}}
                                    <div class="w-full h-full flex flex-col items-center justify-center text-gray-400 dark:text-gray-600">
                                        <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        <span class="text-[10px] font-bold uppercase tracking-wider opacity-70">No Poster</span>
                                    </div>
                                @endif
                            </div>

                            {{-- Content --}}
                            <div class="p-4">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white line-clamp-2 leading-snug group-hover/card:text-blue-600 dark:group-hover/card:text-blue-400 transition-colors h-10">
                                    {{ $program->title }}
                                </h3>
                                @if($program->published_at)
                                    <div class="flex items-center gap-1.5 mt-3 text-xs text-gray-500 dark:text-gray-400">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <span class="font-medium">{{ $program->published_at->format('d M Y') }}</span>
                                    </div>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
    @endif

    {{-- ========================================== --}}
    {{-- 3. JADWAL ACARA (TIMELINE VIEW)            --}}
    {{-- ========================================== --}}
    <section class="py-8 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
                
                {{-- Kolom Kiri: Judul & CTA --}}
                <div class="lg:w-1/3">
                    <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white mb-4">Jangan Lewatkan<br>Acara Favoritmu</h2>
                    <p class="text-gray-600 dark:text-gray-300 mb-6 leading-relaxed text-sm md:text-base">
                        Simak jadwal acara TVRI D.I. Yogyakarta hari ini, <strong>{{ \Carbon\Carbon::now()->locale('id')->isoFormat('dddd, D MMMM Y') }}</strong>.
                    </p>

                    <div class="p-6 bg-gradient-to-br from-blue-700 to-indigo-800 rounded-2xl text-white shadow-lg relative overflow-hidden group">
                        {{-- Decorative Blob --}}
                        <div class="absolute -right-6 -top-6 w-32 h-32 bg-white/10 rounded-full blur-2xl group-hover:bg-white/20 transition-all duration-500"></div>
                        
                        <div class="relative z-10">
                            <div class="flex items-center gap-2 mb-3">
                                <span class="relative flex h-2.5 w-2.5">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-red-500"></span>
                                </span>
                                <span class="text-xs font-bold tracking-wider uppercase text-red-100">Siaran Langsung</span>
                            </div>
                            <h3 class="text-lg md:text-xl font-bold mb-4 leading-tight">Nonton TVRI Jogja Sekarang</h3>
                            <a href="{{ route('streaming') }}" class="inline-flex w-full justify-center items-center gap-2 px-4 py-3 bg-white text-blue-800 font-bold rounded-xl hover:bg-blue-50 transition-colors shadow-sm text-sm">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Tonton Siaran Langsung
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Kanan: List Jadwal --}}
                <div class="lg:w-2/3">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden flex flex-col h-full">
                        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center bg-gray-50/50 dark:bg-gray-700/30">
                            <h3 class="font-bold text-gray-900 dark:text-white text-sm md:text-base">Rundown Hari Ini</h3>
                            <a href="{{ route('publikasi.jadwal') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700 dark:text-blue-400">Lihat Semua Hari &rarr;</a>
                        </div>
                        
                        <div class="divide-y divide-gray-100 dark:divide-gray-700 overflow-y-auto" style="max-height: 400px;">
                            @if(isset($todaySchedules) && $todaySchedules->isNotEmpty())
                                @foreach($todaySchedules as $schedule)
                                    @php
                                        $currentTime = \Carbon\Carbon::now();
                                        $startTime = \Carbon\Carbon::parse($schedule->start_time);
                                        $endTime = $schedule->end_time ? \Carbon\Carbon::parse($schedule->end_time) : null;
                                        
                                        // Logika Status yang Diperbaiki
                                        $isCurrent = false;
                                        $isPast = false;
                                        
                                        // Cek apakah sedang berlangsung
                                        if ($endTime) {
                                            $isCurrent = $currentTime->between($startTime, $endTime);
                                            $isPast = $endTime->lessThan($currentTime);
                                        } else {
                                            // Jika tidak ada end_time, anggap berlangsung 1 jam
                                            $estimatedEnd = $startTime->copy()->addHour();
                                            $isCurrent = $currentTime->between($startTime, $estimatedEnd);
                                            $isPast = $estimatedEnd->lessThan($currentTime);
                                        }

                                        // Tentukan Kelas CSS
                                        if ($isCurrent) {
                                            $rowClass = 'bg-blue-50 dark:bg-blue-900/20 sticky top-0 z-10 border-l-4 border-blue-600 shadow-sm';
                                            $timeClass = 'text-blue-700 dark:text-blue-400 font-bold text-base';
                                            $titleClass = 'text-blue-900 dark:text-white';
                                            $dotClass = 'bg-blue-600 ring-4 ring-blue-100 dark:ring-blue-900 animate-pulse';
                                        } elseif ($isPast) {
                                            $rowClass = 'opacity-60 grayscale-[0.5] hover:opacity-100 transition-opacity bg-gray-50/50 dark:bg-gray-800/50';
                                            $timeClass = 'text-gray-400 line-through decoration-gray-400 text-xs';
                                            $titleClass = 'text-gray-600 dark:text-gray-400';
                                            $dotClass = 'bg-gray-300 dark:bg-gray-600';
                                        } else {
                                            $rowClass = 'hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors';
                                            $timeClass = 'text-gray-900 dark:text-white font-medium text-sm';
                                            $titleClass = 'text-gray-900 dark:text-white';
                                            $dotClass = 'bg-white border-2 border-gray-300 dark:border-gray-500';
                                        }
                                    @endphp

                                    <div class="p-4 flex items-center gap-4 {{ $rowClass }}">
                                        {{-- Jam Tayang --}}
                                        <div class="w-16 text-right flex-shrink-0">
                                            <span class="block {{ $timeClass }}">{{ $startTime->format('H:i') }}</span>
                                            @if($isPast)
                                                <span class="text-[10px] text-gray-400 block">Selesai</span>
                                            @endif
                                        </div>
                                        
                                        {{-- Dot --}}
                                        <div class="relative flex flex-col items-center justify-center">
                                            <div class="w-3 h-3 rounded-full {{ $dotClass }}"></div>
                                        </div>

                                        {{-- Info Program --}}
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-bold truncate {{ $titleClass }}">{{ $schedule->title }}</h4>
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                                {{ $schedule->broadcastCategory->name ?? 'Program TVRI' }}
                                            </p>
                                        </div>
                                        
                                        {{-- Indikator On Air --}}
                                        @if($isCurrent)
                                            <div class="flex-shrink-0">
                                                <span class="px-2 py-1 bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400 text-[10px] font-bold uppercase rounded-md animate-pulse whitespace-nowrap flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 bg-red-600 rounded-full"></span> ON AIR
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="flex flex-col items-center justify-center h-48 text-center text-gray-500 dark:text-gray-400">
                                    <svg class="w-10 h-10 mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    <p class="text-sm">Belum ada jadwal yang dirilis untuk hari ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    {{-- ========================================== --}}
    {{-- 4. INFORMASI PUBLIK (GRID 4 KARTU)         --}}
    {{-- ========================================== --}}
    <section class="py-8 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Layanan dan Informasi Publik</h2>
                <p class="mt-2 text-sm md:text-base text-gray-600 dark:text-gray-400">Transparansi dan akuntabilitas untuk pelayanan yang lebih baik.</p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                
                {{-- Card 1: PPID --}}
                <a href="{{ route('ppid.index') }}" class="group relative p-6 bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 hover:border-blue-200 dark:hover:border-blue-800 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2">PPID</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">Layanan permohonan informasi dan dokumentasi publik.</p>
                    <span class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wide flex items-center gap-1">Lihat <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg></span>
                </a>

                {{-- Card 2: Reformasi Birokrasi --}}
                <a href="{{ route('info-rb.index') }}" class="group relative p-6 bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 hover:border-green-200 dark:hover:border-green-800 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2">Reformasi Birokrasi</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">Tata kelola pemerintahan yang bersih dan akuntabel.</p>
                    <span class="text-xs font-bold text-green-600 dark:text-green-400 uppercase tracking-wide flex items-center gap-1">Cek <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg></span>
                </a>

                {{-- Card 3: Magang --}}
                <a href="{{ route('info-magang.index') }}" class="group relative p-6 bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 hover:border-purple-200 dark:hover:border-purple-800 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2">Magang / PKL</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">Peluang pengembangan karir bagi pelajar dan mahasiswa.</p>
                    <span class="text-xs font-bold text-purple-600 dark:text-purple-400 uppercase tracking-wide flex items-center gap-1">Info <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg></span>
                </a>

                {{-- Card 4: Kunjungan --}}
                <a href="{{ route('info-kunjungan.index') }}" class="group relative p-6 bg-gray-50 dark:bg-gray-800 rounded-2xl border border-gray-100 dark:border-gray-700 hover:border-orange-200 dark:hover:border-orange-800 hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                    <div class="w-10 h-10 bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2">Kunjungan Industri</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2">Prosedur kunjungan studi banding dan tamu dinas.</p>
                    <span class="text-xs font-bold text-orange-600 dark:text-orange-400 uppercase tracking-wide flex items-center gap-1">Tata Cara <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg></span>
                </a>

            </div>
        </div>
    </section>

    {{-- ========================================== --}}
    {{-- 5. POSTINGAN TERBARU (GRID)                --}}
    {{-- ========================================== --}}
    @if(isset($latestPosts) && $latestPosts->isNotEmpty())
    <section class="py-8 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white">Postingan Terbaru</h2>
                <a href="{{ route('postingan.index') }}" class="text-sm font-semibold text-blue-600 hover:text-blue-700 dark:text-blue-400 transition-colors">Lihat Semua</a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
                @foreach($latestPosts as $post)
                <article class="flex flex-col h-full group bg-white dark:bg-gray-800 rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 border border-gray-100 dark:border-gray-700 hover:-translate-y-1">
                    <a href="{{ route('postingan.show', $post->slug) }}" class="block overflow-hidden relative aspect-video">
                        @if($post->featured_image)
                            <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" loading="lazy">
                        @else
                            <div class="w-full h-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" /></svg>
                            </div>
                        @endif
                        
                        @if($post->category)
                            <span class="absolute top-3 left-3 px-2.5 py-1 text-xs font-bold bg-white/95 dark:bg-black/80 backdrop-blur-sm text-gray-900 dark:text-white rounded-md shadow-sm">
                                {{ $post->category->name }}
                            </span>
                        @endif
                    </a>
                    
                    <div class="flex-1 p-5 flex flex-col">
                        <div class="flex items-center gap-2 text-xs text-gray-500 dark:text-gray-400 mb-2">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            <time>{{ $post->created_at->format('d M Y') }}</time>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 leading-snug group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors line-clamp-2">
                            <a href="{{ route('postingan.show', $post->slug) }}">{{ $post->title }}</a>
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-2 mb-4 flex-grow">
                            {{ Str::limit(strip_tags($post->body), 90) }}
                        </p>
                        <a href="{{ route('postingan.show', $post->slug) }}" class="text-sm font-semibold text-blue-600 dark:text-blue-400 hover:underline inline-flex items-center gap-1">
                            Selengkapnya 
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                        </a>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- ========================================== --}}
    {{-- 6. VISI & MISI (COMPACT FEATURE SECTION)   --}}
    {{-- ========================================== --}}
    <section class="py-8 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="relative rounded-3xl overflow-hidden bg-gradient-to-br from-blue-900 via-blue-800 to-indigo-900 shadow-2xl">
                
                {{-- Background Pattern --}}
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\'60\' height=\'60\' viewBox=\'0 0 60 60\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cg fill=\'none\' fill-rule=\'evenodd\'%3E%3Cg fill=\'%23ffffff\' fill-opacity=\'1\'%3E%3Cpath d=\'M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
                </div>

                <div class="relative z-10 p-8 md:p-12 lg:p-16">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center">
                        
                        {{-- Kolom Kiri: Teks Visi --}}
                        <div class="text-center lg:text-left">
                            <h2 class="text-2xl md:text-4xl font-extrabold text-white mb-6 leading-tight">
                                Menjadi Lembaga Penyiaran Publik Kelas Dunia
                            </h2>
                            <p class="text-base md:text-lg text-blue-100 leading-relaxed mb-8 opacity-90">
                                Memotivasi dan memberdayakan masyarakat melalui program informasi, pendidikan, dan hiburan yang sehat serta perekat sosial.
                            </p>
                            
                            <div class="flex flex-wrap justify-center lg:justify-start gap-4">
                                <a href="{{ route('visi-misi') }}" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-blue-900 bg-white rounded-xl hover:bg-blue-50 transition-colors shadow-lg group">
                                    Lihat Misi Kami
                                    <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                </a>
                                <a href="{{ route('sejarah') }}" class="inline-flex items-center justify-center px-6 py-3 text-sm font-bold text-white bg-transparent border-2 border-white/30 rounded-xl hover:bg-white/10 transition-colors">
                                    Sejarah TVRI
                                </a>
                            </div>
                        </div>

                        {{-- Kolom Kanan: Highlights --}}
                        <div class="hidden lg:block relative">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white/10 backdrop-blur-md border border-white/10 p-5 rounded-2xl transform translate-y-4">
                                    <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center mb-3 text-blue-200">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                                    </div>
                                    <h4 class="text-white font-bold mb-1">Informatif</h4>
                                    <p class="text-xs text-blue-200">Menyajikan berita aktual dan terpercaya</p>
                                </div>

                                <div class="bg-white/10 backdrop-blur-md border border-white/10 p-5 rounded-2xl">
                                    <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center mb-3 text-green-200">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                    </div>
                                    <h4 class="text-white font-bold mb-1">Edukatif</h4>
                                    <p class="text-xs text-blue-200">Konten pendidikan yang mencerdaskan</p>
                                </div>

                                <div class="bg-white/10 backdrop-blur-md border border-white/10 p-5 rounded-2xl transform translate-y-4">
                                    <div class="w-10 h-10 bg-purple-500/20 rounded-lg flex items-center justify-center mb-3 text-purple-200">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    </div>
                                    <h4 class="text-white font-bold mb-1">Menghibur</h4>
                                    <p class="text-xs text-blue-200">Hiburan sehat untuk keluarga</p>
                                </div>

                                <div class="bg-white/10 backdrop-blur-md border border-white/10 p-5 rounded-2xl">
                                    <div class="w-10 h-10 bg-orange-500/20 rounded-lg flex items-center justify-center mb-3 text-orange-200">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                                    </div>
                                    <h4 class="text-white font-bold mb-1">Perekat Sosial</h4>
                                    <p class="text-xs text-blue-200">Menjaga persatuan bangsa</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ========================================== --}}
    {{-- 7. HUBUNGI KAMI (DYNAMIC FOOTER SECTION)   --}}
    {{-- ========================================== --}}
    @if(isset($contactInfo) && $contactInfo)
    <section class="py-8 bg-white dark:bg-gray-900 border-b border-gray-100 dark:border-gray-800">
        <div class="mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-10 lg:gap-16">
                
                {{-- Kolom Kiri: Informasi Kontak --}}
                <div class="flex-1">
                    <h2 class="text-2xl md:text-3xl font-extrabold text-gray-900 dark:text-white mt-2 mb-6">
                        Tetap Terhubung Bersama TVRI Stasiun Yogyakarta
                    </h2>
                    
                    <div class="space-y-6">
                        {{-- Alamat --}}
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white">Alamat TVRI</h4>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mt-1 leading-relaxed">
                                    {{ $contactInfo->address }}
                                </p>
                            </div>
                        </div>

                        {{-- Email --}}
                        @if($contactInfo->email)
                        <div class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white">Email Instansi</h4>
                                <a href="mailto:{{ $contactInfo->email }}" class="text-gray-600 dark:text-gray-400 text-sm mt-1 hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                    {{ $contactInfo->email }}
                                </a>
                            </div>
                        </div>
                        @endif

                        {{-- Telepon (Diperbarui sesuai Database Baru) --}}
                        <div class="pt-4 border-t border-gray-200 dark:border-gray-700">
                            <h4 class="font-bold text-gray-900 dark:text-white mb-4">Layanan Telepon</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                
                                {{-- 1. Telepon Admin --}}
                                <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wide font-semibold mb-1">Administrasi</p>
                                    <p class="font-mono text-gray-800 dark:text-gray-200 font-medium text-sm">{{ $contactInfo->admin_phone }}</p>
                                </div>

                                {{-- 2. Telepon Kerjasama --}}
                                <div class="p-3 rounded-lg bg-indigo-50 dark:bg-indigo-900/10 border border-indigo-100 dark:border-indigo-800/30">
                                    <p class="text-xs text-indigo-600 dark:text-indigo-400 uppercase tracking-wide font-semibold mb-1">Kerjasama / PU</p>
                                    <p class="font-mono text-gray-800 dark:text-gray-200 font-medium text-sm">{{ $contactInfo->partnership_phone }}</p>
                                </div>
                                
                                {{-- Hotline WA --}}
                                <div class="sm:col-span-2 p-3 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-800/50 flex items-center justify-between">
                                    <div>
                                        <p class="text-xs text-green-700 dark:text-green-400 uppercase tracking-wide font-semibold mb-1">Hotline (WhatsApp)</p>
                                        <p class="font-mono text-gray-900 dark:text-white font-bold">{{ $contactInfo->hotline_phone }}</p>
                                    </div>
                                    {{-- Membersihkan nomor HP untuk link WA (Hapus spasi, strip, dll) --}}
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $contactInfo->hotline_phone) }}" target="_blank" class="p-2 bg-green-500 text-white rounded-full hover:bg-green-600 transition-colors shadow-sm group">
                                        <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Peta Lokasi (Embed) --}}
                <div class="flex-1 md:w-1/2 lg:w-3/5">
                    <div class="h-full min-h-[300px] md:min-h-[400px] bg-gray-200 dark:bg-gray-700 rounded-2xl overflow-hidden shadow-lg border border-gray-200 dark:border-gray-600 relative group">
                        
                        {{-- Logika Pengecekan URL Peta --}}
                        @if($contactInfo->google_maps_embed && filter_var($contactInfo->google_maps_embed, FILTER_VALIDATE_URL))
                            {{-- Peta Utama (Dari Database) --}}
                            <iframe 
                                src="{{ $contactInfo->google_maps_embed }}" 
                                width="100%" 
                                height="100%" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade"
                                class="absolute inset-0 w-full h-full grayscale group-hover:grayscale-0 transition-all duration-500">
                            </iframe>
                        @else
                            {{-- Fallback Peta --}}
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3953.187392686856!2d110.3621453!3d-7.7699313!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7a584a6e5a5b5b%3A0x6b5a5b5b5b5b5b5b!2sTVRI%20Yogyakarta!5e0!3m2!1sid!2sid!4v1625630000000!5m2!1sid!2sid" 
                                width="100%" 
                                height="100%" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade"
                                class="absolute inset-0 w-full h-full grayscale group-hover:grayscale-0 transition-all duration-500">
                            </iframe>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </section>
    @endif

</x-layout>