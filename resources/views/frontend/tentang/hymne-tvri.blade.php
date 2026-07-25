<x-layout>
    <x-slot:title>{{ $title ?? 'Himne dan Mars TVRI' }}</x-slot:title>

    {{-- 1. Load CSS Video.js & Custom 3D Style --}}
    @push('styles')
        <link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />
        <style>
            /* Custom Style Video.js agar responsif penuh */
            .video-js {
                width: 100%;
                height: 100%;
                border-radius: 0.75rem; /* rounded-xl */
            }
            .vjs-big-play-button {
                top: 50% !important;
                left: 50% !important;
                transform: translate(-50%, -50%);
                background-color: rgba(220, 38, 38, 0.9) !important; /* Red-600 */
                border-color: #fff !important;
                border-radius: 50% !important;
                width: 2.5em !important;
                height: 2.5em !important;
                line-height: 2.5em !important;
                font-size: 2em !important;
            }

            /* CSS 3D Effect */
            .hymne-card-3d-left, 
            .hymne-card-3d-right {
                transform: none;
                transition: transform 0.5s ease, box-shadow 0.5s ease;
            }

            @media (min-width: 768px) {
                .hymne-card-3d-left { transform: rotateY(-10deg) rotateX(5deg); }
                .hymne-card-3d-right { transform: rotateY(10deg) rotateX(5deg); }
                
                .group:hover .hymne-card-3d-left,
                .group:hover .hymne-card-3d-right {
                    transform: rotateY(0deg) rotateX(0deg) scale(1.02);
                    z-index: 10;
                    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                }
            }
        </style>
    @endpush

    <div class="px-4 sm:px-6 lg:px-8 py-8 sm:py-12 lg:py-16 space-y-12 md:space-y-20"> 
        
        {{-- HEADER HALAMAN --}}
        <div class="text-center max-w-3xl mx-auto mb-8 sm:mb-12">
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white tracking-tight">
                {{ $title ?? 'Himne dan Mars TVRI' }}
            </h1>
            <p class="mt-4 text-base sm:text-lg text-gray-500 dark:text-gray-400 leading-relaxed">
                Kumpulan lagu identitas, mars, dan himne yang menjadi semangat Lembaga Penyiaran Publik TVRI.
            </p>
        </div>

        {{-- LOOP DATA HYMNE --}}
        <div class="space-y-12 md:space-y-16">
            @forelse($hymnes as $hymne)
                @php
                    // 1. Logika Deteksi ID YouTube (Regex)
                    $videoId = null;
                    if ($hymne->link) {
                        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $hymne->link, $matches)) {
                            $videoId = $matches[1];
                        }
                    }

                    // 2. Logika Layout Zig-Zag
                    $isOdd = $loop->odd;
                    $bgGradient = $isOdd
                        ? 'bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800'
                        : 'bg-gradient-to-br from-purple-50 to-pink-100 dark:from-gray-900 dark:to-gray-800';

                    $orderMedia = $isOdd ? '' : 'md:order-last';
                    $orderText  = $isOdd ? '' : 'md:order-first';
                    $class3D    = $isOdd ? 'hymne-card-3d-left' : 'hymne-card-3d-right';
                @endphp

                <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-0">
                        
                        {{-- 1. BAGIAN MEDIA (VIDEO) --}}
                        <div class="md:col-span-2 relative aspect-video md:aspect-auto md:min-h-[350px] overflow-visible p-6 sm:p-8 flex items-center justify-center {{ $bgGradient }} {{ $orderMedia }}">
                            
                            <div class="relative w-full" style="perspective: 1000px;">
                                <div class="relative w-full aspect-video bg-black rounded-xl shadow-2xl overflow-hidden {{ $class3D }}">
                                    
                                    {{-- A. YOUTUBE --}}
                                    @if($videoId)
                                        <iframe class="w-full h-full"
                                                src="https://www.youtube.com/embed/{{ $videoId }}?rel=0" 
                                                title="{{ $hymne->title }}"
                                                frameborder="0" 
                                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                                allowfullscreen>
                                        </iframe>

                                    {{-- B. LOCAL VIDEO (Gunakan str_ends_with native PHP) --}}
                                    @elseif($hymne->link && (str_ends_with($hymne->link, '.mp4') || str_ends_with($hymne->link, '.m3u8')))
                                        <video id="hymne-video-{{ $hymne->id }}" 
                                               class="video-js vjs-default-skin vjs-big-play-centered" 
                                               controls 
                                               preload="metadata" 
                                               poster="{{ $hymne->poster ? Storage::url($hymne->poster) : '' }}"
                                               data-setup='{"fluid": true}'>
                                            <source src="{{ $hymne->link }}" type="{{ str_ends_with($hymne->link, '.m3u8') ? 'application/x-mpegURL' : 'video/mp4' }}">
                                        </video>

                                    {{-- C. AUDIO / POSTER ONLY --}}
                                    @else
                                        @if($hymne->poster)
                                            <img class="w-full h-full object-cover" 
                                                 src="{{ Storage::url($hymne->poster) }}" 
                                                 alt="{{ $hymne->title }}">
                                            <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/10 transition-colors pointer-events-none">
                                                <div class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center shadow-lg">
                                                    <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex flex-col items-center justify-center h-full text-gray-500 bg-gray-100 dark:bg-gray-800">
                                                <svg class="w-16 h-16 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                                </svg>
                                                <span class="text-sm font-medium">Audio Only</span>
                                            </div>
                                        @endif
                                    @endif

                                    <div class="absolute inset-0 ring-1 ring-inset ring-white/10 rounded-xl pointer-events-none"></div>
                                </div>
                            </div>
                        </div>

                        {{-- 2. BAGIAN TEKS --}}
                        <div class="md:col-span-3 p-6 sm:p-8 lg:p-12 flex flex-col justify-center bg-white dark:bg-gray-800 {{ $orderText }}">
                            
                            @if($hymne->info)
                                <div class="flex mb-4">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                                        {{ $hymne->info }}
                                    </span>
                                </div>
                            @endif

                            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-4 leading-tight">
                                {{ $hymne->title }}
                            </h2>

                            {{-- Trix Content (Menggunakan clean() untuk keamanan XSS) --}}
                            <div class="prose prose-base sm:prose-lg dark:prose-invert text-gray-600 dark:text-gray-400 max-w-none">
                                {!! clean($hymne->synopsis) !!}
                            </div>

                            @if($hymne->link && !$videoId && !str_ends_with($hymne->link, '.mp4') && !str_ends_with($hymne->link, '.m3u8'))
                                <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                                    <a href="{{ $hymne->link }}" target="_blank" class="inline-flex items-center gap-2 text-blue-600 dark:text-blue-400 font-medium hover:underline">
                                        <span>Buka Tautan Eksternal</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>

            @empty
                <div class="text-center py-16 bg-gray-50 dark:bg-gray-800/50 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum ada data</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Data Himne dan Mars belum ditambahkan.</p>
                </div>
            @endforelse
        </div>

        {{-- PAGINASI --}}
        @if($hymnes->hasPages())
            <div class="mt-10 mb-12 px-2 sm:px-0">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    {{-- Hapus class 'flex justify-center' agar pagination bisa melebar (justify-between) --}}
                    {{ $hymnes->withQueryString()->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        @endif

    </div>

    @push('scripts')
        <script src="https://vjs.zencdn.net/8.10.0/video.min.js"></script>
    @endpush
</x-layout>