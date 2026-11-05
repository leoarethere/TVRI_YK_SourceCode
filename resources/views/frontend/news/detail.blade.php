<x-layout>
    {{-- Slot Title & Meta Description (SEO) --}}
    <x-slot:title>{{ $news->meta_title ?? $news->title }}</x-slot:title>
    <x-slot:meta_description>
        {{ $news->meta_description ?? Str::limit(strip_tags($news->body), 150) }}
    </x-slot:meta_description>

    {{-- Open Graph: Gambar & Tipe Artikel --}}
    <x-slot:og_image>{{ $news->featured_image ? Storage::url($news->featured_image) : asset('img/og-default.png') }}</x-slot:og_image>
    <x-slot:og_type>article</x-slot:og_type>

    {{-- SEO Structured Data --}}
    @if($news)
    @php
        $newsSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'NewsArticle',
            'headline' => $news->title,
            'description' => Str::limit(strip_tags($news->body), 160),
            'datePublished' => \App\Support\Time::toWib($news->published_at ?? $news->created_at)?->toIso8601String(),
            'author' => [
                '@type' => 'Person',
                'name' => $news->user->name ?? 'Admin',
            ],
            'image' => $news->featured_image ? Storage::url($news->featured_image) : '',
        ];
        $jsonLdOptions = JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT;
    @endphp
    <script type="application/ld+json">{!! json_encode($newsSchema, $jsonLdOptions) !!}</script>
    @endif

    {{-- WRAPPER UTAMA --}}
    <div class="min-h-screen px-4 sm:px-6 lg:px-8">
        
        <article class="mx-auto w-full bg-white dark:bg-gray-800 rounded-2xl sm:rounded-3xl shadow-sm border border-gray-100 dark:border-gray-700 overflow-hidden transition-colors duration-300">
            
            {{-- 1. BREADCRUMB --}}
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

                        {{-- Berita (Index) --}}
                        <li>
                            <a href="{{ route('berita.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Berita</a>
                        </li>

                        {{-- Separator --}}
                        <li><svg class="w-3 h-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 6 10"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/></svg></li>

                        {{-- Kategori --}}
                        @if($news->newsCategory)
                            <li>
                                <a href="{{ route('berita.index', ['category' => $news->newsCategory->slug]) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors font-medium text-blue-600 dark:text-blue-400">
                                    {{ $news->newsCategory->name }}
                                </a>
                            </li>
                        @endif
                    </ol>
                </nav>
            </div>

            {{-- 2. HEADER ARTIKEL --}}
            <header class="px-5 sm:px-8 lg:px-10 pb-6 sm:pb-8">
                {{-- Judul Responsif --}}
                <h1 class="mt-4 text-2xl sm:text-3xl lg:text-4xl/tight font-extrabold text-gray-900 dark:text-white tracking-tight">
                    {{ $news->title }}
                </h1>

                {{-- Meta Info: Author & Date --}}
                <div class="mt-6 flex flex-wrap items-center gap-4 sm:gap-6 py-4 border-t border-gray-100 dark:border-gray-700">
                    
                    {{-- Author --}}
                    @if ($news->user)
                    <div class="flex items-center gap-3 group">
                        <img class="w-10 h-10 sm:w-11 sm:h-11 rounded-full ring-2 ring-white dark:ring-gray-800 shadow-sm" 
                             src="{{ $news->user->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($news->user->name).'&background=random&color=fff' }}" 
                             alt="{{ $news->user->name }}">
                        <div class="flex flex-col">
                            <span class="text-sm font-bold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                {{ $news->user->name }}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                Penulis
                            </span>
                        </div>
                    </div>
                    @endif

                    {{-- Tanggal --}}
                    <div class="flex items-center gap-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400 bg-gray-50 dark:bg-gray-700/50 px-3 py-1.5 rounded-full">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        @php
                            $newsTimeWib = \App\Support\Time::toWib($news->created_at);
                        @endphp
                        <time datetime="{{ $newsTimeWib?->toIso8601String() }}">
                            {{ $newsTimeWib?->translatedFormat('d F Y, H:i') ?? '-' }} WIB
                        </time>
                    </div>
                </div>

                {{-- Link Sumber Berita --}}
                @if($news->link_berita)
                    <div class="mt-4">
                        <a href="{{ $news->link_berita }}" target="_blank" rel="noopener noreferrer"
                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-blue-700 bg-blue-50 border border-blue-200 rounded-xl hover:bg-blue-100 hover:border-blue-300 dark:text-blue-400 dark:bg-blue-900/20 dark:border-blue-800 dark:hover:bg-blue-900/40 transition-all">
                            <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                            Lihat Sumber Berita
                            <svg class="w-3.5 h-3.5 flex-shrink-0 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                            </svg>
                        </a>
                    </div>
                @endif
            </header>

            {{-- 3. FEATURED IMAGE --}}
            @if ($news->featured_image && Storage::exists($news->featured_image))
                <div class="w-full px-5 sm:px-8 lg:px-10 pb-6 sm:pb-8">
                    <figure class="relative w-full aspect-[2/1] lg:max-h-[400px] xl:max-h-[450px] overflow-hidden rounded-xl shadow-lg bg-gray-900 dark:bg-black">
                        {{-- Blurred Background Layer --}}
                        <img src="{{ Storage::url($news->featured_image) }}" alt="{{ $news->title }}" 
                             alt="" 
                             class="absolute inset-0 w-full h-full object-cover blur-2xl opacity-60 scale-110"
                             aria-hidden="true">
                        
                        {{-- Main Image Layer (Fit without stretching) --}}
                        <img src="{{ Storage::url($news->featured_image) }}" alt="{{ $news->title }}" 
                             alt="{{ $news->title }}" 
                             class="absolute inset-0 w-full h-full object-contain transition-transform duration-700 hover:scale-[1.02] z-10"
                             loading="lazy">
                             
                        @if($news->caption_image)
                             <figcaption class="absolute bottom-0 left-0 right-0 bg-black/60 text-white text-xs px-4 py-2 truncate z-20 backdrop-blur-sm">
                                 {{ $news->caption_image }}
                             </figcaption>
                        @endif
                    </figure>
                </div>
            @endif

            {{-- 4. KONTEN ARTIKEL --}}
            <div class="px-5 sm:px-8 lg:px-10 py-8 sm:py-10">
                {{-- Body Artikel (Typography Plugin) --}}
                <div class="prose prose-base md:prose-lg max-w-none 
                            text-gray-700 dark:text-gray-300 leading-relaxed
                            prose-headings:font-bold prose-headings:text-gray-900 dark:prose-headings:text-white prose-headings:tracking-tight
                            prose-a:text-blue-600 dark:prose-a:text-blue-400 hover:prose-a:text-blue-700 hover:prose-a:underline
                            prose-strong:text-gray-900 dark:prose-strong:text-white
                            prose-img:rounded-xl prose-img:shadow-lg prose-img:mx-auto prose-img:w-full
                            prose-blockquote:border-l-4 prose-blockquote:border-blue-500 prose-blockquote:bg-gray-50 dark:prose-blockquote:bg-gray-800/50 prose-blockquote:py-2 prose-blockquote:px-4 prose-blockquote:rounded-r-lg">
                    {!! clean($news->body) !!}
                </div>
            </div>

            {{-- 5. FOOTER & SHARE --}}
            <div class="px-5 sm:px-8 lg:px-10 py-6 sm:py-8 bg-gray-50 dark:bg-gray-900/50 border-t border-gray-100 dark:border-gray-700 flex flex-col sm:flex-row justify-between items-center gap-6">
                
                {{-- Tombol Kembali --}}
                <a href="{{ route('berita.index') }}" 
                   class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 hover:text-blue-600 hover:border-blue-300 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 transition-all shadow-sm">
                    <svg class="w-4 h-4 transition-transform group-hover:-translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Semua Berita
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
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($news->title) }}" target="_blank" rel="noopener noreferrer"
                           class="flex-1 sm:flex-none flex justify-center items-center p-2.5 rounded-lg sm:rounded-full bg-black dark:bg-gray-700 text-white hover:bg-gray-800 dark:hover:bg-gray-600 transition-colors shadow-sm" aria-label="Bagikan di X">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                        </a>

                        {{-- WhatsApp --}}
                        <a href="https://wa.me/?text={{ urlencode($news->title . ' ' . request()->url()) }}" target="_blank" rel="noopener noreferrer"
                           class="flex-1 sm:flex-none flex justify-center items-center p-2.5 rounded-lg sm:rounded-full bg-green-500 text-white hover:bg-green-600 transition-colors shadow-sm" aria-label="Bagikan di WhatsApp">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"></path></svg>
                        </a>
                    </div>
                </div>

            </div>

            {{-- 6. KOMENTAR --}}
            <div class="px-5 sm:px-8 lg:px-10 pb-10">
                <x-comments :model="$news" />
            </div>

        </article>
    </div>
</x-layout>
