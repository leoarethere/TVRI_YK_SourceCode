<x-layout>
    {{-- Slot Title & Meta Description (SEO) --}}
    <x-slot:title>{{ $title ?? 'Detail Program Siaran' }}</x-slot:title>
    <x-slot:meta_description>
        {{ Str::limit(strip_tags($broadcast->synopsis ?? ''), 150, '...') ?: 'Program siaran TVRI D.I. Yogyakarta' }}
    </x-slot:meta_description>

    {{-- Open Graph: Gambar Poster & Tipe --}}
    <x-slot:og_image>{{ $broadcast->poster ? Storage::url($broadcast->poster) : asset('img/og-default.png') }}</x-slot:og_image>
    <x-slot:og_type>article</x-slot:og_type>

    {{-- SEO Structured Data --}}
    @if($broadcast)
    @php
        $broadcastSchema = [
            '@context' => 'https://schema.org',
            '@type' => $broadcast->youtubeEmbedUrl ? 'VideoObject' : 'BroadcastEvent',
            'name' => $broadcast->title,
            'description' => $broadcast->synopsis ?? 'Program siaran',
            'datePublished' => ($broadcast->published_at ?? $broadcast->created_at)->toIso8601String(),
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('app.name', 'Laravel'),
            ],
            'eventStatus' => $broadcast->is_active
                ? 'https://schema.org/EventScheduled'
                : 'https://schema.org/EventMovedOnline',
        ];

        if ($broadcast->youtubeEmbedUrl) {
            $broadcastSchema['embedUrl'] = $broadcast->youtubeEmbedUrl;
            $broadcastSchema['thumbnailUrl'] = $broadcast->poster ? Storage::url($broadcast->poster) : '';
        }

        if ($broadcast->broadcastCategory) {
            $broadcastSchema['genre'] = $broadcast->broadcastCategory->name;
        }

        $jsonLdOptions = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
    @endphp
    <script type="application/ld+json">{!! json_encode($broadcastSchema, $jsonLdOptions) !!}</script>
    @endif

    {{-- WRAPPER UTAMA --}}
    <div class="min-h-screen px-4 sm:px-6 lg:px-8">

        <article class="mx-auto w-full bg-white dark:bg-gray-800 rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-300">

            {{-- 1. BREADCRUMB (Scrollable on Mobile) --}}
            <div class="px-5 sm:px-8 lg:px-10 pt-6 sm:pt-8 pb-2">
                <nav class="flex text-sm text-gray-500 dark:text-gray-400 overflow-x-auto scrollbar-hide">
                    <ol class="inline-flex items-center space-x-1 md:space-x-2 whitespace-nowrap">

                        {{-- Beranda --}}
                        <li>
                            <a href="{{ route('home') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                                <span class="hidden sm:inline">Beranda</span>
                            </a>
                        </li>

                        {{-- Separator --}}
                        <li><svg class="w-3 h-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 6 10"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/></svg></li>

                        {{-- Program Siaran --}}
                        <li>
                            <a href="{{ route('penyiaran.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Program Siaran</a>
                        </li>

                        {{-- Separator --}}
                        <li><svg class="w-3 h-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 6 10"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/></svg></li>

                        {{-- Kategori --}}
                        @if($broadcast->broadcastCategory)
                            <li>
                                <a href="{{ route('penyiaran.index', ['category' => $broadcast->broadcastCategory->slug]) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors font-medium text-blue-600 dark:text-blue-400">
                                    {{ $broadcast->broadcastCategory->name }}
                                </a>
                            </li>
                        @endif
                    </ol>
                </nav>
            </div>

            {{-- 2. HEADER PROGRAM --}}
            <header class="px-5 sm:px-8 lg:px-10 pb-6 sm:pb-8">
                {{-- Judul Responsif --}}
                <h1 class="mt-4 text-2xl sm:text-3xl lg:text-4xl/tight font-extrabold text-gray-900 dark:text-white tracking-tight">
                    {{ $broadcast->title }}
                </h1>

                {{-- Meta Info Wrapper --}}
                <div class="mt-6 flex flex-wrap items-center gap-4 sm:gap-6 py-4 border-t border-gray-100 dark:border-gray-700">

                    {{-- Status Badge --}}
                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-10 h-10 rounded-full {{ $broadcast->is_active ? 'bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                            @if($broadcast->is_active)
                                <svg class="w-5 h-5 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.636 18.364a9 9 0 010-12.728m12.728 0a9 9 0 010 12.728m-9.9-2.829a5 5 0 010-7.07m7.072 0a5 5 0 010 7.07M13 12a1 1 0 11-2 0 1 1 0 012 0z" />
                                </svg>
                            @else
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            @endif
                        </div>
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-gray-900 dark:text-white">
                                {{ $broadcast->is_active ? 'On Air' : 'Selesai' }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">Status</span>
                        </div>
                    </div>

                    {{-- Tanggal --}}
                    <div class="flex items-center gap-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 px-3 py-1.5 rounded-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <time datetime="{{ $broadcast->published_at?->toIso8601String() ?? $broadcast->created_at->toIso8601String() }}">
                            {{ $broadcast->published_at ? $broadcast->published_at->translatedFormat('d F Y') : $broadcast->created_at->translatedFormat('d F Y') }}
                        </time>
                    </div>
                </div>
            </header>

            {{-- 3. MEDIA (HERO SECTION) --}}
            @php
                $url = $broadcast->youtube_link ?? $broadcast->youtubeEmbedUrl;
                $videoId = null;
                if ($url) {
                    $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
                    if (preg_match($pattern, $url, $matches)) {
                        $videoId = $matches[1];
                    }
                }
            @endphp

            @if ($videoId)
                {{-- Video Player Responsive 16:9 --}}
                <div class="w-full bg-black">
                    <figure class="relative w-full aspect-video mx-auto max-w-5xl">
                        <iframe class="absolute top-0 left-0 w-full h-full"
                                src="https://www.youtube.com/embed/{{ $videoId }}?rel=0"
                                title="YouTube video player"
                                frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen>
                        </iframe>
                    </figure>
                </div>
            @elseif ($broadcast->poster)
                {{-- Poster Image Responsive --}}
                <div class="w-full px-5 sm:px-8 lg:px-10 pb-6 sm:pb-8">
                    <figure class="relative w-full aspect-[2/1] lg:max-h-[400px] xl:max-h-[450px] overflow-hidden rounded-xl shadow-lg bg-gray-900 dark:bg-black">
                        {{-- Blurred Background Layer --}}
                        <img src="{{ Storage::url($broadcast->poster) }}"
                             alt=""
                             class="absolute inset-0 w-full h-full object-cover blur-2xl opacity-60 scale-110"
                             aria-hidden="true">

                        {{-- Main Image Layer (Fit without stretching) --}}
                        <img src="{{ Storage::url($broadcast->poster) }}"
                             alt="{{ $broadcast->title }}"
                             class="absolute inset-0 w-full h-full object-contain transition-transform duration-700 hover:scale-[1.02] z-10"
                             loading="lazy">
                    </figure>
                </div>
            @endif

            {{-- 4. KONTEN (SINOPSIS) --}}
            <div class="px-5 sm:px-8 lg:px-10 py-8 sm:py-10">
                <div class="prose prose-base md:prose-lg max-w-none
                            text-gray-700 dark:text-gray-300 leading-relaxed
                            prose-headings:font-bold prose-headings:text-gray-900 dark:prose-headings:text-white prose-headings:tracking-tight
                            prose-p:text-gray-700 dark:prose-p:text-gray-300">

                    <h2 class="text-xl font-bold mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                        Sinopsis Program
                    </h2>

                    @if ($broadcast->synopsis)
                        {!! nl2br(e($broadcast->synopsis)) !!}
                    @else
                        <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-700/50 text-gray-500 italic text-center">
                            Belum ada sinopsis untuk program ini.
                        </div>
                    @endif
                </div>
            </div>

            {{-- 5. FOOTER & SHARE --}}
            <div class="px-5 sm:px-8 lg:px-10 py-6 sm:py-8 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-6">

                {{-- Tombol Kembali --}}
                <a href="{{ route('penyiaran.index') }}"
                   class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:text-blue-600 hover:border-blue-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-all shadow-sm">
                    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Semua Program Siaran
                </a>

                {{-- Social Share --}}
                <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400 hidden sm:block">Bagikan:</span>
                    <div class="flex items-center justify-center gap-2 w-full sm:w-auto">

                        {{-- Facebook --}}
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" rel="noopener noreferrer"
                           class="flex-1 sm:flex-none flex justify-center items-center p-2.5 rounded-lg sm:rounded-full bg-blue-600 text-white hover:bg-blue-700 transition-colors shadow-sm" aria-label="Bagikan di Facebook">
                             <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"></path></svg>
                        </a>

                        {{-- Twitter / X --}}
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($broadcast->title) }}" target="_blank" rel="noopener noreferrer"
                           class="flex-1 sm:flex-none flex justify-center items-center p-2.5 rounded-lg sm:rounded-full bg-black dark:bg-gray-700 text-white hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors shadow-sm" aria-label="Bagikan di X">
                             <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>

                        {{-- WhatsApp --}}
                        <a href="https://wa.me/?text={{ urlencode($broadcast->title . ' ' . request()->url()) }}" target="_blank" rel="noopener noreferrer"
                           class="flex-1 sm:flex-none flex justify-center items-center p-2.5 rounded-lg sm:rounded-full bg-green-500 text-white hover:bg-green-600 transition-colors shadow-sm" aria-label="Bagikan di WhatsApp">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.885-9.884 9.885m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"></path></svg>
                        </a>
                    </div>
                </div>

            </div>

            {{-- 6. KOMENTAR --}}
            <div class="px-5 sm:px-8 lg:px-10 pb-10">
                <x-comments :model="$broadcast" />
            </div>

        </article>
    </div>
</x-layout>
