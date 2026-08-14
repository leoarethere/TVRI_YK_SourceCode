<x-layout>
    <x-slot:title>Informasi Magang / PKL</x-slot:title>

    {{-- KONTAINER UTAMA --}}
    <div class="min-h-screen px-4 sm:px-6 lg:px-8">

        {{-- HERO SECTION --}}
        <div class="relative rounded-3xl overflow-hidden mb-6 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 dark:from-blue-900 dark:via-blue-950 dark:to-indigo-950 shadow-xl">
            {{-- Background Pattern --}}
            <div class="absolute inset-0 bg-grid-white/[0.05] bg-[size:24px_24px]"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>

            <div class="relative px-6 py-12 lg:px-12 lg:py-20 text-center z-10">
                <div class="max-w-3xl mx-auto">
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-4 drop-shadow-sm">
                        Informasi Magang / PKL
                    </h1>
                    <p class="text-blue-100 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">
                        Temukan peluang pengembangan karir dan pengalaman kerja nyata bersama TVRI Stasiun D.I. Yogyakarta. FAQ di bawah ini membantu menjawab pertanyaan seputar program magang kami.
                    </p>
                </div>
            </div>
        </div>

        {{-- BAGIAN FILTER & SORTING --}}
        <div class="mb-6">
            <form method="GET" action="{{ route('info-magang.index') }}" class="mx-auto">
                <div class="flex flex-col md:flex-row justify-between items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">

                    {{-- Judul Seksi --}}
                    <div class="w-full md:w-auto">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Semua Informasi Magang / PKL
                        </h2>
                    </div>

                    {{-- Group Filter --}}
                    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">

                        {{-- Search Input --}}
                        <div class="relative w-full sm:w-64">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>
                            <input type="text"
                                   name="search"
                                   value="{{ request('search') }}"
                                   class="block w-full py-2.5 pl-10 pr-4 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                   placeholder="Cari info magang...">
                        </div>

                        {{-- Sorting --}}
                        <div class="relative w-full sm:w-48">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                                </svg>
                            </div>
                            <select name="sort" onchange="this.form.submit()"
                                    class="block w-full py-2.5 pl-10 pr-8 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white cursor-pointer">
                                <option value="latest" @selected(request('sort', 'latest') == 'latest')>Terbaru</option>
                                <option value="oldest" @selected(request('sort') == 'oldest')>Terlama</option>
                                <option value="title_asc" @selected(request('sort') == 'title_asc')>Judul A-Z</option>
                                <option value="title_desc" @selected(request('sort') == 'title_desc')>Judul Z-A</option>
                            </select>
                        </div>

                    </div>
                </div>
            </form>

            {{-- Info Hasil Pencarian / Filter Aktif --}}
            @if(request()->hasAny(['search', 'sort']))
                <div class="mx-auto mt-4 flex flex-wrap items-center justify-between gap-4 px-2">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Menampilkan <strong>{{ $items->total() }}</strong> informasi
                        @if(request('search'))
                            untuk "<strong>{{ request('search') }}</strong>"
                        @endif
                    </div>

                    <a href="{{ route('info-magang.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-full hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-900/50 transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Reset Filter
                    </a>
                </div>
            @endif
        </div>

        {{-- CONTENT GRID --}}
        <div class="mx-auto mb-12">
            @if($items->count() > 0)
                {{-- Grid: 1 kolom mobile, 2 kolom large screen (Card Horizontal) --}}
                <div class="grid gap-6 grid-cols-1 lg:grid-cols-2">
                    @foreach($items as $item)

                        {{-- CARD ITEM (HORIZONTAL LAYOUT) --}}
                        <div class="group relative flex flex-col md:flex-row bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-xl transition-all duration-300 hover:-translate-y-1">

                            {{-- 1. GAMBAR / ICON (KIRI) --}}
                            <a href="{{ $item->source_link }}" target="_blank" rel="noopener noreferrer" class="relative w-full md:w-48 md:min-w-[12rem] aspect-video md:aspect-auto shrink-0 bg-gray-100 dark:bg-gray-700 block overflow-hidden">
                                @if($item->cover_image)
                                    <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
                                         src="{{ Storage::url($item->cover_image) }}"
                                         alt="{{ $item->title }}"
                                         loading="lazy"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">

                                    {{-- Fallback jika error load gambar --}}
                                    <div class="hidden w-full h-full items-center justify-center bg-gray-100 dark:bg-gray-700">
                                        <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @else
                                    {{-- Placeholder Default --}}
                                    <div class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-gray-700 group-hover:bg-indigo-50 dark:group-hover:bg-gray-600 transition-colors">
                                        <svg class="w-12 h-12 text-indigo-300 dark:text-gray-500 group-hover:text-indigo-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                    </div>
                                @endif

                                {{-- Overlay Hover --}}
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/5 transition-colors duration-300"></div>
                            </a>

                            {{-- 2. KONTEN (KANAN) --}}
                            <div class="flex-1 p-5 flex flex-col justify-between">
                                <div>
                                    {{-- Meta Data --}}
                                    <div class="flex items-center justify-between mb-3">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wide bg-indigo-100 text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                            Magang
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            {{ $item->created_at->format('d M Y') }}
                                        </span>
                                    </div>

                                    {{-- Judul --}}
                                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                        <a href="{{ $item->source_link }}" target="_blank" rel="noopener noreferrer">
                                            {{ $item->title }}
                                        </a>
                                    </h3>

                                    {{-- Deskripsi --}}
                                    <div class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 mb-4 leading-relaxed">
                                        {{ strip_tags($item->description) }}
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="pt-4 border-t border-gray-100 dark:border-gray-700 flex items-center gap-3">

                                    {{-- Tombol Lihat Informasi --}}
                                    <a href="{{ $item->source_link }}" target="_blank" rel="noopener noreferrer"
                                       class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-2 text-sm font-semibold text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transition-all shadow-sm">
                                        <span>Lihat Informasi</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                    </a>

                                    {{-- Tombol Salin Link (Alpine.js) --}}
                                    <div x-data="{
                                        copied: false,
                                        copyFailed: false,
                                        copyUsingFallback(text) {
                                            const textArea = document.createElement('textarea');
                                            try {
                                                textArea.value = text;
                                                document.body.appendChild(textArea);
                                                textArea.select();
                                                if (!document.execCommand('copy')) {
                                                    throw new Error('Browser menolak perintah salin.');
                                                }
                                            } finally {
                                                textArea.remove();
                                            }
                                        },
                                        async copyToClipboard() {
                                            const text = @js($item->source_link);
                                            this.copied = false;
                                            this.copyFailed = false;

                                            try {
                                                if (navigator.clipboard && typeof navigator.clipboard.writeText === 'function') {
                                                    await navigator.clipboard.writeText(text);
                                                } else {
                                                    this.copyUsingFallback(text);
                                                }
                                                this.copied = true;
                                            } catch (error) {
                                                this.copyFailed = true;
                                            }

                                            setTimeout(() => {
                                                this.copied = false;
                                                this.copyFailed = false;
                                            }, 2000);
                                        }
                                    }">
                                        <button type="button" @click="copyToClipboard()"
                                                class="p-2 text-gray-500 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-400 dark:hover:bg-gray-600 rounded-lg transition-colors border border-gray-200 dark:border-gray-600 relative group/tooltip"
                                                :class="{
                                                    'text-green-600 bg-green-50 border-green-200 dark:bg-green-900/30 dark:border-green-800 dark:text-green-400': copied,
                                                    'text-red-600 bg-red-50 border-red-200 dark:bg-red-900/30 dark:border-red-800 dark:text-red-400': copyFailed
                                                }"
                                            title="Salin tautan" aria-label="Salin tautan">

                                            {{-- Ikon Default --}}
                                            <svg x-show="!copied" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"></path></svg>

                                            {{-- Ikon Sukses --}}
                                            <svg x-show="copied" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display: none;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>

                                            {{-- Tooltip --}}
                                            <span class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 text-xs text-white bg-gray-900 rounded opacity-0 group-hover/tooltip:opacity-100 transition-opacity whitespace-nowrap pointer-events-none"
                                                  x-text="copied ? 'Tersalin!' : (copyFailed ? 'Gagal menyalin' : 'Salin Link')">
                                            </span>
                                        </button>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Empty State --}}
                <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 dark:bg-gray-700/50 mb-6 text-gray-400 dark:text-gray-500">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Informasi Tidak Ditemukan</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-8">
                        @if(request('search'))
                            Tidak ada informasi magang yang cocok dengan kata kunci <strong>"{{ request('search') }}"</strong>.
                        @else
                            Belum ada informasi magang/PKL yang tersedia saat ini.
                        @endif
                    </p>
                    @if(request('search'))
                        <a href="{{ route('info-magang.index') }}" class="inline-flex items-center px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors shadow-sm">
                            Reset Pencarian
                        </a>
                    @endif
                </div>
            @endif

            {{-- Pagination --}}
            @if($items->hasPages())
                <div class="mt-10 mb-12 px-2 sm:px-0">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                        {{-- Hapus class 'flex justify-center' agar pagination bisa melebar (justify-between) --}}
                        {{ $items->withQueryString()->links('vendor.pagination.tailwind') }}
                    </div>
                </div>
            @endif
        </div>

        {{-- ========================================== --}}
        {{-- SECTION FAQ (PERTANYAAN UMUM)              --}}
        {{-- ========================================== --}}
        @if(isset($faqs) && $faqs->count() > 0)
            <div class="mt-20 mb-12 mx-auto">
                <div class="text-center mb-10">
                    <span class="text-blue-600 dark:text-blue-400 font-bold tracking-wider uppercase text-sm">Bantuan</span>
                    <h2 class="text-2xl md:text-3xl font-bold text-gray-900 dark:text-white mt-2">
                        Pertanyaan yang Sering Diajukan (FAQ)
                    </h2>
                    <p class="text-gray-500 dark:text-gray-400 mt-2">
                        Jawaban untuk pertanyaan umum seputar proses magang di TVRI Yogyakarta.
                    </p>
                </div>

                <div class="space-y-4" x-data="{ activeAccordion: null }">
                    @foreach($faqs as $faq)
                        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden transition-all duration-300 hover:shadow-md">
                            <button
                                type="button"
                                @click="activeAccordion = (activeAccordion === {{ $faq->id }} ? null : {{ $faq->id }})"
                                :aria-expanded="activeAccordion === {{ $faq->id }}"
                                aria-controls="magang-faq-{{ $faq->id }}"
                                class="flex items-center justify-between w-full p-5 text-left bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors focus:outline-none">

                                <span class="text-lg font-semibold text-gray-900 dark:text-white pr-4">
                                    {{ $faq->question }}
                                </span>

                                {{-- Icon Chevron Rotasi --}}
                                <svg class="w-5 h-5 text-gray-500 transition-transform duration-300 transform"
                                     :class="{ 'rotate-180': activeAccordion === {{ $faq->id }} }"
                                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>

                            {{-- Konten Jawaban (Slide Down) --}}
                            <div id="magang-faq-{{ $faq->id }}" x-show="activeAccordion === {{ $faq->id }}"
                                 x-collapse
                                 x-cloak
                                 class="border-t border-gray-100 dark:border-gray-700 dark:bg-gray-900/30">
                                <div class="p-5 text-gray-600 dark:text-gray-300 leading-relaxed prose dark:prose-invert max-w-none text-sm md:text-base">
                                    {!! nl2br(e($faq->answer)) !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layout>
