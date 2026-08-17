<x-layout>
    <x-slot:title>{{ $title ?? 'Postingan' }}</x-slot:title>

    {{-- KONTAINER UTAMA --}}
    <div class="min-h-screen px-4 sm:px-6 lg:px-8">
        <div class="mb-6">
            <x-hero-posts />
        </div>
        
        {{-- BAGIAN FILTER & SORTING --}}
        <div class="mb-6">
            <form method="GET" action="{{ route('postingan.index') }}" class="mx-auto">
                @if(request('search'))
                    <input type="hidden" name="search" value="{{ request('search') }}">
                @endif

                <div class="flex flex-col md:flex-row justify-between items-center gap-4 p-4 bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700">
                    
                    {{-- Judul Seksi --}}
                    <div class="w-full md:w-auto">
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path></svg>
                            Semua Postingan
                        </h2>
                    </div>

                    {{-- Group Filter --}}
                    <div class="flex flex-col sm:flex-row gap-3 w-full md:w-auto">
                        
                        {{-- Filter Kategori --}}
                        <div class="relative w-full sm:w-48">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7A2 2 0 0121 12v5a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2h3m0 0l-3 3m0 0l3 3m-3-3h5"></path></svg>
                            </div>
                            <select name="category" onchange="this.form.submit()" 
                                    class="block w-full py-2.5 pl-10 pr-8 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white cursor-pointer">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->slug }}" @selected(request('category') == $category->slug)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Sorting --}}
                        <div class="relative w-full sm:w-48">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path></svg>
                            </div>
                            <select name="sort" onchange="this.form.submit()" 
                                    class="block w-full py-2.5 pl-10 pr-8 text-sm text-gray-900 bg-gray-50 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white cursor-pointer">
                                <option value="latest" @selected(request('sort', 'latest') == 'latest')>Terbaru</option>
                                <option value="oldest" @selected(request('sort') == 'oldest')>Terlama</option>
                                <option value="popular" @selected(request('sort') == 'popular')>Populer</option>
                                <option value="title_asc" @selected(request('sort') == 'title_asc')>Judul A-Z</option>
                                <option value="title_desc" @selected(request('sort') == 'title_desc')>Judul Z-A</option>
                            </select>
                        </div>

                    </div>
                </div>
            </form>

            {{-- Info Hasil Pencarian / Filter Aktif --}}
            @if(request()->hasAny(['search', 'category', 'sort', 'author']))
                <div class="mx-auto mt-4 flex flex-wrap items-center justify-between gap-4 px-2">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        Menampilkan <strong>{{ $posts->total() }}</strong> artikel
                        @if(request('search'))
                            untuk "<strong>{{ request('search') }}</strong>"
                        @endif
                        @if(request('category'))
                            kategori "<strong>{{ $categories->firstWhere('slug', request('category'))->name ?? request('category') }}</strong>"
                        @endif
                    </div>
                    
                    <a href="{{ route('postingan.index') }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-red-700 bg-red-100 rounded-full hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-900/50 transition-colors">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Reset Filter
                    </a>
                </div>
            @endif
        </div>
        
        {{-- GRID POSTINGAN --}}
        <div class="mb-12">
            @if($posts->count() > 0)
                <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($posts as $post)
                        <article class="flex flex-col h-full bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-xl transition-all duration-300 group hover:-translate-y-1">
                            
                            {{-- Image Thumbnail --}}
                            <a href="{{ route('postingan.show', $post->slug) }}" class="relative w-full aspect-video overflow-hidden block bg-gray-100 dark:bg-gray-900">
                                @if ($post->featured_image && Storage::exists($post->featured_image))
                                    {{-- Blurred Background --}}
                                    <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" class="absolute inset-0 w-full h-full object-cover blur-xl opacity-60 scale-110" aria-hidden="true">
                                    {{-- Main Image --}}
                                    <img src="{{ Storage::url($post->featured_image) }}" alt="{{ $post->title }}" 
                                         alt="{{ $post->title }}" 
                                         class="relative w-full h-full object-contain transition-transform duration-500 group-hover:scale-105"
                                         loading="lazy">
                                @else
                                    {{-- Placeholder Pattern --}}
                                    <div class="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-gray-700">
                                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif

                                {{-- Category Badge --}}
                                @if($post->category)
                                    <div class="absolute top-3 left-3 z-10">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider text-white shadow-md backdrop-blur-md bg-opacity-95 {{ $post->category->colorSolidClasses }}">
                                            {{ $post->category->name }}
                                        </span>
                                    </div>
                                @endif
                            </a>

                            {{-- Content --}}
                            <div class="flex flex-col flex-grow p-5">
                                {{-- Meta Data --}}
                                <div class="flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 mb-3">
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        {{ $post->created_at->format('d M Y') }}
                                    </span>
                                    @if($post->user)
                                        <span class="flex items-center gap-1 truncate max-w-[120px]">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                            {{ $post->user->name }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Title --}}
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2 line-clamp-2 leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    <a href="{{ route('postingan.show', $post->slug) }}">
                                        {{ $post->title }}
                                    </a>
                                </h3>

                                {{-- Excerpt --}}
                                <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-3 mb-4 flex-grow leading-relaxed">
                                    {{ Str::limit($post->excerpt ? $post->excerpt : strip_tags($post->body), 120, '...') }}
                                </p>

                                {{-- Read More --}}
                                <div class="pt-4 mt-auto border-t border-gray-100 dark:border-gray-700">
                                    <a href="{{ route('postingan.show', $post->slug) }}" class="inline-flex items-center text-sm font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 transition-colors underline group/link">
                                        Lihat Selengkapnya
                                    </a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                {{-- Empty State --}}
                <div class="text-center py-20 bg-white dark:bg-gray-800 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 dark:bg-gray-700/50 mb-6 text-gray-400 dark:text-gray-500">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Artikel Tidak Ditemukan</h3>
                    <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-8">
                        @if(request('search'))
                            Tidak ada artikel yang cocok dengan kata kunci <strong>"{{ request('search') }}"</strong>.
                        @elseif(request('category'))
                            Belum ada artikel dalam kategori ini.
                        @else
                            Belum ada berita atau artikel yang dipublikasikan.
                        @endif
                    </p>
                    <a href="{{ route('postingan.index') }}" class="inline-flex items-center px-6 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-xl hover:bg-blue-700 transition-colors shadow-sm">
                        Muat Ulang
                    </a>
                </div>
            @endif
        </div>

        {{-- PAGINATION --}}
        @if ($posts->hasPages())
            <div class="mt-10 mb-12 px-2 sm:px-0">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    {{-- Hapus class 'flex justify-center' agar pagination bisa melebar (justify-between) --}}
                    {{ $posts->withQueryString()->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        @endif

    </div>
</x-layout>
