<x-layout>
    <x-slot:title>
        {{ $title ?? 'Program Siaran' }}
        @if(request('category') && isset($activeCategory))
            - {{ $activeCategory->name }}
        @endif
    </x-slot:title>

    {{-- KONTAINER UTAMA --}}
    <div class="min-h-screen px-4 sm:px-6 lg:px-8">

        {{-- HERO SECTION --}}
        <div class="relative rounded-3xl overflow-hidden mb-8 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 dark:from-blue-900 dark:via-blue-950 dark:to-indigo-950 shadow-xl">
            {{-- Background Pattern --}}
            <div class="absolute inset-0 bg-grid-white/[0.05] bg-[size:24px_24px]"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
            
            <div class="relative px-6 py-12 lg:px-12 lg:py-20 text-center z-10">
                <div class="max-w-3xl mx-auto">
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold text-white mb-4 drop-shadow-sm">
                        @if(isset($activeCategory))
                            {{ $activeCategory->name }}
                        @else
                            Program Siaran
                        @endif
                    </h1>
                    <p class="text-blue-100 text-base sm:text-lg max-w-2xl mx-auto leading-relaxed">
                        @if(isset($activeCategory))
                            Jelajahi program unggulan TVRI D.I. Yogyakarta dalam kategori {{ $activeCategory->name }}.
                        @else
                            Jelajahi berbagai program unggulan TVRI D.I. Yogyakarta yang menghadirkan informasi, hiburan, dan edukasi berkualitas.
                        @endif
                    </p>
                </div>
            </div>
        </div>
        
        {{-- BAGIAN FILTER & PENCARIAN --}}
        <div class="mb-8 space-y-6">
            
            {{-- Form Pencarian Standard --}}
            <form method="GET" action="{{ route('penyiaran.index') }}" class="mx-auto">
                {{-- Preserve Category if exists --}}
                @if(request('category'))
                    <input type="hidden" name="category" value="{{ request('category') }}">
                @endif

                <div class="flex flex-col md:flex-row gap-3 p-2 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                    {{-- Input Search --}}
                    <div class="relative flex-grow">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               class="block w-full py-3 pl-11 pr-4 text-sm text-gray-900 bg-transparent border-none focus:ring-0 dark:text-white placeholder-gray-500 dark:placeholder-gray-400" 
                               placeholder="Cari nama program...">
                    </div>

                    {{-- Divider --}}
                    <div class="hidden md:block w-px bg-gray-200 dark:bg-gray-700 my-2"></div>

                    {{-- Button --}}
                    <button type="submit" class="px-6 py-3 text-sm font-semibold text-white bg-blue-600 rounded-xl hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800 transition-colors shadow-sm">
                        Cari Program
                    </button>
                </div>
            </form>

            {{-- Navigasi Kategori (Scrollable Pills) --}}
            <div class="relative group">
                <div class="absolute -left-4 top-0 bottom-0 w-12 bg-gradient-to-r from-gray-50 dark:from-gray-900 to-transparent z-10 pointer-events-none md:hidden"></div>
                <div class="overflow-x-auto scrollbar-hide -mx-4 px-4 lg:mx-0 lg:px-0">
                    <nav class="flex gap-2 lg:gap-3 pb-2" style="min-width: min-content;">
                        <a href="{{ route('penyiaran.index') }}"
                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-full transition-all duration-200 whitespace-nowrap border shadow-sm
                           {{ !request('category') 
                               ? 'bg-blue-600 border-blue-600 text-white hover:bg-blue-700' 
                               : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                            Semua Kategori
                        </a>
                        @foreach ($categories as $category)
                        <a href="{{ route('penyiaran.index', ['category' => $category->slug]) }}"
                           class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium rounded-full transition-all duration-200 whitespace-nowrap border shadow-sm
                           {{ request('category') == $category->slug 
                               ? 'bg-blue-600 border-blue-600 text-white hover:bg-blue-700' 
                               : 'bg-white dark:bg-gray-800 border-gray-200 dark:border-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                            <span class="w-2 h-2 rounded-full {{ $category->color_classes }}"></span>
                            {{ $category->name }}
                        </a>
                        @endforeach
                    </nav>
                </div>
                <div class="absolute -right-4 top-0 bottom-0 w-12 bg-gradient-to-l from-gray-50 dark:from-gray-900 to-transparent z-10 pointer-events-none md:hidden"></div>
            </div>
        </div>
        
        {{-- PROGRAMS GRID --}}
        <div class="pb-8">
            @if($broadcasts->count() > 0)
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 lg:gap-6">
                    @foreach ($broadcasts as $broadcast)
                        <a href="{{ route('penyiaran.show', $broadcast) }}" 
                           class="group block bg-white dark:bg-gray-800 rounded-2xl overflow-hidden transition-all duration-300 hover:-translate-y-1.5 border border-gray-200 dark:border-gray-700 hover:shadow-lg hover:border-blue-400 dark:hover:border-blue-600">
                            
                            {{-- Poster --}}
                            <div class="relative overflow-hidden bg-gray-100 dark:bg-gray-900 aspect-[3/4]">
                                @if ($broadcast->poster)
                                    <img src="{{ Storage::url($broadcast->poster) }}" 
                                         alt="{{ $broadcast->title }}" 
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" 
                                         loading="lazy">
                                         
                                    {{-- Overlay Hover --}}
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-3">
                                        <span class="inline-flex items-center gap-1 text-white text-xs font-bold transform translate-y-2 group-hover:translate-y-0 transition-transform duration-300">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z" /><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd" /></svg>
                                            Lihat Detail
                                        </span>
                                    </div>
                                @else
                                    {{-- Placeholder Image --}}
                                    <div class="w-full h-full flex flex-col items-center justify-center bg-gray-100 dark:bg-gray-800 text-gray-400 dark:text-gray-600">
                                        <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                        <span class="text-xs font-medium">No Poster</span>
                                    </div>
                                @endif
                                
                                {{-- Badge Kategori --}}
                                {{-- @if($broadcast->broadcastCategory)
                                <div class="absolute top-2 left-2 z-10">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold shadow-sm uppercase tracking-wide {{ $broadcast->broadcastCategory->color_classes }} backdrop-blur-sm bg-opacity-90">
                                        {{ $broadcast->broadcastCategory->name }}
                                    </span>
                                </div>
                                @endif --}}

                                {{-- Badge Status --}}
                                <div class="absolute top-2 right-2 z-10">
                                    @if($broadcast->is_active)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded text-[10px] font-bold bg-green-600 text-white shadow-sm border border-green-500 uppercase tracking-wide">
                                            <span class="w-1.5 h-1.5 bg-white rounded-full animate-pulse"></span>
                                            On Air
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Content --}}
                            <div class="p-3">
                                <h3 class="text-sm font-bold text-gray-900 dark:text-white line-clamp-2 leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors h-10">
                                    {{ $broadcast->title }}
                                </h3>
                                @if($broadcast->published_at)
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    {{ \App\Support\Time::toWib($broadcast->published_at)?->format('d M Y') ?? '-' }}
                                </p>
                                @endif
                            </div>
                        </a>
                    @endforeach
                </div>
            @else
                {{-- EMPTY STATE --}}
                <div class="text-center py-16 bg-white dark:bg-gray-800 rounded-3xl border border-dashed border-gray-200 dark:border-gray-700">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 dark:bg-gray-700/50 mb-6 text-gray-400 dark:text-gray-500">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Program Tidak Ditemukan</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-8">
                        @if(request('category')) 
                            Tidak ada program siaran dalam kategori ini.
                        @elseif(request('search'))
                            Tidak ada program yang cocok dengan kata kunci <strong>"{{ request('search') }}"</strong>.
                        @else
                            Belum ada program siaran yang tersedia saat ini.
                        @endif
                    </p>
                    @if(request('category') || request('search'))
                        <a href="{{ route('penyiaran.index') }}" class="inline-flex items-center px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors shadow-sm">
                            Tampilkan Semua Program
                        </a>
                    @endif
                </div>
            @endif
        </div>

        {{-- PAGINATION --}}
        @if ($broadcasts->hasPages())
            <div class="mt-10 mb-12 px-2 sm:px-0">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    {{-- Hapus class 'flex justify-center' agar pagination bisa melebar (justify-between) --}}
                    {{ $broadcasts->withQueryString()->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        @endif

    </div>

    <style>
        .scrollbar-hide::-webkit-scrollbar { display: none; }
        .scrollbar-hide { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</x-layout>
