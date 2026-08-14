<x-layout>
    <x-slot:title>{{ $title ?? 'Live Streaming' }}</x-slot:title>

    @push('styles')
        <link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />
        <style>
            /* Custom Play Button di Tengah */
            .video-js .vjs-big-play-button {
                top: 50%; 
                left: 50%; 
                transform: translate(-50%, -50%);
                background-color: rgba(37, 99, 235, 0.9); /* Blue-600 */
                border: none; 
                border-radius: 50%; 
                width: 3em; 
                height: 3em; 
                line-height: 3em; 
                font-size: 1.5em;
            }
            /* Rounded corners untuk player */
            .video-js { border-radius: 1rem; overflow: hidden; }
        </style>
    @endpush

    <div class="min-h-screen px-4 sm:px-6 lg:px-8">
        <div class="mx-auto">
            
            {{-- Breadcrumb --}}
            <nav class="flex text-sm text-gray-500 dark:text-gray-400 mb-6">
                <ol class="inline-flex items-center space-x-2">
                    <li><a href="{{ route('home') }}" class="hover:text-blue-600 dark:hover:text-blue-400">Beranda</a></li>
                    <li>
                        <svg class="w-3 h-3 mx-1 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 6 10"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/></svg>
                    </li>
                    <li class="text-gray-900 dark:text-white font-medium">Live Streaming</li>
                </ol>
            </nav>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- PLAYER SECTION --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-black rounded-2xl shadow-xl overflow-hidden aspect-video relative group ring-1 ring-gray-900/5 dark:ring-gray-700">
                        {{-- Badge LIVE --}}
                        <div class="absolute top-4 left-4 z-20 px-3 py-1 bg-red-600/90 backdrop-blur-sm text-white text-xs font-bold rounded-full flex items-center gap-2 animate-pulse">
                            <span class="w-2 h-2 bg-white rounded-full"></span> LIVE
                        </div>
                        
                        <video id="tvri-player" class="video-js vjs-fluid vjs-theme-city" controls preload="auto" 
                            {{-- UBAH BAGIAN INI --}}
                            poster="{{ asset('img/loginbanner.jpeg') }}" 
                            data-setup='{"fluid": true}'>

                            <source src="{{ $stream->stream_url }}" type="application/x-mpegURL">
                            <p class="vjs-no-js">To view this video please enable JavaScript.</p>
                        </video>
                    </div>

                    {{-- Info Stream --}}
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $stream->title }}</h1>
                        <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ $stream->description }}</p>
                    </div>
                </div>

                {{-- SIDEBAR INFO --}}
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 border border-gray-100 dark:border-gray-700 shadow-sm">
                        <h3 class="font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Info Saluran
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Kualitas</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">HD 720p/1080i</span>
                            </div>
                            <div class="flex justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                                <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                                <span class="text-xs font-bold px-2 py-1 bg-green-100 text-green-700 rounded-full dark:bg-green-900/30 dark:text-green-400">ONLINE</span>
                            </div>
                        </div>
                        
                        <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                             <p class="text-xs text-center text-gray-400 dark:text-gray-500">
                                Mengalami gangguan? <a href="https://klik.tvri.go.id/" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">Buka Pemutar Alternatif</a>
                            </p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Script Video.js --}}
    @push('scripts')
        <script src="https://vjs.zencdn.net/8.10.0/video.min.js"></script>
    @endpush
</x-layout>
