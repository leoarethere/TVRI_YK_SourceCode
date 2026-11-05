<x-dashboard-layout>
    <x-slot:title>
        Kelola Program Siaran
    </x-slot:title>

    {{-- Data Alpine.js --}}
    <div x-data="{
        // State untuk modal hapus penyiaran
        deleteModal: false,
        deleteBroadcastId: null,
        deleteBroadcastTitle: '',

        // State untuk modal kategori
        isCategoryModalOpen: false,
        modalMode: 'create',
        modalTitle: 'Tambah Kategori Baru',
        modalAction: @js(route('dashboard.penyiaran-kategori.store')),
        updateUrlTemplate: @js(route('dashboard.penyiaran-kategori.update', ':id')),

        categoryData: {
            id: null,
            name: '',
            slug: '',
            color: 'blue'
        },

        // State untuk modal hapus kategori
        deleteCategoryModal: false,
        deleteCategoryId: null,
        deleteCategoryName: '',
        deleteCategoryError: '',

        // Fungsi Slugify
        generateSlug() {
            this.categoryData.slug = this.categoryData.name
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
        },

        // Modal Functions (Create/Edit/Delete Kategori)
        openCreateModal() {
            this.modalMode = 'create';
            this.modalTitle = 'Tambah Kategori Baru';
            this.modalAction = @js(route('dashboard.penyiaran-kategori.store'));
            this.categoryData = { id: null, name: '', slug: '', color: 'blue' };
            this.isCategoryModalOpen = true;
            document.body.style.overflow = 'hidden';
        },

        openEditModal(category) {
            this.modalMode = 'edit';
            this.modalTitle = 'Edit Kategori';
            this.modalAction = this.updateUrlTemplate.replace(':id', category.id);
            this.categoryData = {
                id: category.id,
                name: category.name,
                slug: category.slug,
                color: category.color
            };
            this.isCategoryModalOpen = true;
            document.body.style.overflow = 'hidden';
        },

        closeCategoryModal() {
            this.isCategoryModalOpen = false;
            this.deleteCategoryModal = false;
            this.deleteCategoryId = null;
            this.deleteCategoryName = '';
            this.deleteCategoryError = '';
            document.body.style.overflow = '';
        },

        openDeleteCategoryModal(category) {
            this.deleteCategoryId = category.id;
            this.deleteCategoryName = category.name;
            this.deleteCategoryError = '';
            this.deleteCategoryModal = true;
            document.body.style.overflow = 'hidden';
        },

        closeDeleteCategoryModal() {
            this.deleteCategoryModal = false;
            this.deleteCategoryId = null;
            this.deleteCategoryName = '';
            this.deleteCategoryError = '';
            document.body.style.overflow = this.isCategoryModalOpen ? 'hidden' : '';
        },

        // Modal Functions (Delete Broadcast)
        openDeleteModal(broadcastId, broadcastTitle) {
            this.deleteBroadcastId = broadcastId;
            this.deleteBroadcastTitle = broadcastTitle;
            this.deleteModal = true;
            document.body.style.overflow = 'hidden';
        },

        closeDeleteModal() {
            this.deleteModal = false;
            document.body.style.overflow = '';
        }
    }"
    class="pb-6"
    @if ($errors->has('name') || $errors->has('slug') || $errors->has('color'))
        x-init="isCategoryModalOpen = true; document.body.style.overflow = 'hidden';"
    @endif
    >

        {{-- HEADER HALAMAN --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6 pt-2">
            <div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                    Kelola Program Siaran
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelola program, video, dan informasi siaran TVRI.
                </p>
            </div>

            {{-- Tombol Aksi Header --}}
            <div class="dashboard-header-actions flex flex-wrap items-center gap-3">
                <button
                    @click="openCreateModal()"
                    type="button"
                    class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors whitespace-nowrap shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7A2 2 0 0121 12v5a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2h3m0 0l-3 3m0 0l3 3m-3-3h5"></path></svg>
                    Kelola Kategori
                </button>

                <a href="{{ route('dashboard.penyiaran.create') }}"
                    class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 transition-colors whitespace-nowrap shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path></svg>
                    Tambah Program Siaran
                </a>
            </div>
        </div>

        {{-- FORM FILTER --}}
        <form method="GET" action="{{ route('dashboard.penyiaran.index') }}" class="mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" class="block w-full p-3 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Cari penyiaran...">
                </div>

                <select name="category" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-48 p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected(request('category') == $category->slug)>{{ $category->name }}</option>
                    @endforeach
                </select>

                <select name="sort" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-48 p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    <option value="latest" @selected(request('sort', 'latest') == 'latest')>Terbaru</option>
                    <option value="oldest" @selected(request('sort') == 'oldest')>Terlama</option>
                </select>
                <div class="flex gap-2">
                    <button type="submit" class="w-full md:w-auto px-5 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300">Cari</button>
                    @if(request()->hasAny(['search', 'category', 'sort']))
                        <a href="{{ route('dashboard.penyiaran.index') }}" class="w-full md:w-auto px-5 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 text-center">Reset</a>
                    @endif
                </div>
            </div>
        </form>

        {{-- INFO HASIL FILTER --}}
        @if(request()->hasAny(['search', 'category', 'sort']))
            <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                Menampilkan {{ $broadcasts->total() }} hasil
                @if(request('search'))
                    untuk pencarian <strong class="text-gray-900 dark:text-white">"{{ request('search') }}"</strong>
                @endif
                @if(request('category'))
                    dalam kategori <strong class="text-gray-900 dark:text-white">"{{ $categories->where('slug', request('category'))->first()->name ?? request('category') }}"</strong>
                @endif
            </div>
        @endif

        {{-- ALERT SUCCESS
        @if(session('success'))
            <div class="mb-6 p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-800 transition-all duration-300" role="alert">
                <div class="flex items-center">
                    <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <div>
                        <span class="font-medium">Sukses!</span> {{ session('success') }}
                    </div>
                </div>
            </div>
        @endif --}}

        {{-- TABEL --}}
        <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="dashboard-data-table w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-center w-12">No</th>
                            <th scope="col" class="px-4 py-3 text-left w-72">Program</th>
                            <th scope="col" class="px-4 py-3 text-left w-40">Status</th>
                            <th scope="col" class="px-4 py-3 text-left min-w-[200px]">Sinopsis</th>
                            <th scope="col" class="px-4 py-3 text-center w-24">Link</th>
                            <th scope="col" class="px-4 py-3 text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($broadcasts as $broadcast)
                            <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">

                                <td class="px-4 py-4 align-top text-center font-medium text-gray-900 dark:text-white">
                                    {{ $loop->iteration + $broadcasts->firstItem() - 1 }}
                                </td>

                                {{-- Program (Poster + Judul + Kategori) --}}
                                <td class="px-4 py-4 align-top">
                                    <div class="flex items-start gap-4">
                                        {{-- Poster Thumbnail --}}
                                        <div class="flex-shrink-0 w-16 h-20 bg-gray-100 dark:bg-gray-700 rounded-md ring-1 ring-gray-200 dark:ring-gray-700 overflow-hidden flex items-center justify-center">
                                            @if ($broadcast->poster)
                                                <img src="{{ Storage::url($broadcast->poster) }}" alt="Poster" class="w-full h-full object-cover">
                                            @else
                                                <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            @endif
                                        </div>

                                        {{-- Judul & Kategori --}}
                                        <div class="flex flex-col">
                                            <p class="font-bold text-gray-900 dark:text-white line-clamp-2 leading-tight mb-2" title="{{ $broadcast->title }}">
                                                {{ $broadcast->title }}
                                            </p>
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium w-fit {{ $broadcast->broadcastCategory->color_classes ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                                {{ $broadcast->broadcastCategory->name ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Status (Produksi & Publikasi) --}}
                                <td class="px-4 py-4 align-top">
                                    <div class="flex flex-col gap-2">
                                        {{-- Status Produksi --}}
                                        @if($broadcast->is_active)
                                            <span class="inline-flex items-center justify-center px-2 py-1 rounded text-[10px] font-bold tracking-wide bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300 border border-green-200 dark:border-green-800 w-fit">
                                                TAYANG
                                            </span>
                                        @else
                                            <span class="inline-flex items-center justify-center px-2 py-1 rounded text-[10px] font-bold tracking-wide bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 border border-gray-200 dark:border-gray-600 w-fit">
                                                SELESAI
                                            </span>
                                        @endif

                                        {{-- Status Publikasi --}}
                                        @if ($broadcast->status === 'published')
                                            @php
                                                $broadcastPublishedWib = \App\Support\Time::toWib($broadcast->published_at);
                                            @endphp
                                            <span class="inline-flex items-center gap-1 text-xs text-gray-600 dark:text-gray-400" title="Dipublikasikan pada {{ $broadcastPublishedWib?->format('d M Y H:i') }}">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                {{ $broadcastPublishedWib?->format('d M Y') ?? '-' }}
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 text-xs text-orange-600 dark:text-orange-400 font-medium">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                Draf
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                {{-- Sinopsis --}}
                                <td class="px-4 py-4 align-top max-w-xs">
                                    <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-3 leading-relaxed" title="{{ $broadcast->synopsis ?? '' }}">
                                        {{ $broadcast->synopsis ?? '-' }}
                                    </p>
                                </td>

                                {{-- Link Youtube --}}
                                <td class="px-4 py-4 align-top text-center">
                                    @if ($broadcast->youtube_link)
                                        <a href="{{ $broadcast->youtube_link }}" target="_blank" rel="noopener noreferrer"
                                            title="Tonton di YouTube"
                                            aria-label="Tonton di YouTube"
                                            class="inline-flex items-center justify-center p-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 dark:hover:bg-red-900/50 transition-colors group">
                                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                            </svg>
                                        </a>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-600 text-sm">-</span>
                                    @endif
                                </td>

                                {{-- Tombol Aksi --}}
                                <td class="px-4 py-4 align-top text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('dashboard.penyiaran.edit', $broadcast) }}"
                                           class="p-2 text-yellow-600 bg-yellow-50 rounded-lg hover:bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-900/30 dark:hover:bg-yellow-900/50 transition-colors"
                                           title="Edit" aria-label="Edit program siaran">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>

                                        <button type="button" @click.prevent="openDeleteModal({{ $broadcast->id }}, @js($broadcast->title))"
                                                class="p-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 dark:text-red-400 dark:bg-red-900/30 dark:hover:bg-red-900/50 transition-colors"
                                                title="Hapus" aria-label="Hapus program siaran">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-base font-medium">Tidak ada data penyiaran ditemukan</p>
                                        <p class="text-sm mt-1">Silakan tambahkan data baru.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- PAGINASI --}}
            @if($broadcasts->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $broadcasts->links() }}
                </div>
            @endif
        </div>

        {{-- MODAL HAPUS PENYIARAN --}}
        <div x-show="deleteModal"
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/75 backdrop-blur-sm flex items-center justify-center p-4"
             @keydown.escape.window="closeDeleteModal()">

            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full overflow-hidden" @click.away="closeDeleteModal()">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Konfirmasi Hapus</h3>
                    <button @click="closeDeleteModal()" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="p-6">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div class="flex-1">
                            <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Anda yakin ingin menghapus?</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Data <span class="font-medium text-gray-900 dark:text-white" x-text="deleteBroadcastTitle"></span> akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <button @click="closeDeleteModal()" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700 transition-colors">
                        Batal
                    </button>

                    <form :action="@js(route('dashboard.penyiaran.destroy', ':id')).replace(':id', deleteBroadcastId)" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800 transition-colors">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL KELOLA KATEGORI --}}
        <div x-show="isCategoryModalOpen"
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/75 backdrop-blur-sm flex items-center justify-center p-4"
             @keydown.escape.window="deleteCategoryModal ? closeDeleteCategoryModal() : closeCategoryModal()">

            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col" @click.away="closeCategoryModal()">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="modalTitle"></h3>
                    <button @click="closeCategoryModal()" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-0 overflow-hidden h-full">

                    {{-- Form Input --}}
                    <form :action="modalAction" method="POST" @submit="if(modalMode === 'edit') { $event.target.querySelector('[name=_method]').value = 'PUT'; }" class="p-6 border-r border-gray-200 dark:border-gray-700 overflow-y-auto">
                        @csrf
                        <input type="hidden" name="_method" value="POST">

                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4" x-text="modalMode === 'edit' ? 'Edit Kategori' : 'Tambah Kategori Baru'"></h4>

                        {{-- Nama Kategori --}}
                        <div class="mb-4">
                            <label for="cat-name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Nama Kategori <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="cat-name" name="name" x-model="categoryData.name" @input="generateSlug()"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="Contoh: Rilis Pers" required>
                            @error('name') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Slug --}}
                        <div class="mb-4">
                            <label for="cat-slug" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Slug <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="cat-slug" name="slug" x-model="categoryData.slug"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white font-mono" required>
                            @error('slug') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Warna --}}
                        <div class="mb-4">
                            <label for="cat-color" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Warna Badge
                            </label>
                            <select id="cat-color" name="color" x-model="categoryData.color"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="blue">Biru</option>
                                <option value="pink">Pink</option>
                                <option value="green">Hijau</option>
                                <option value="yellow">Kuning</option>
                                <option value="indigo">Indigo</option>
                                <option value="purple">Ungu</option>
                                <option value="red">Merah</option>
                                <option value="gray">Abu-abu</option>
                            </select>
                            @error('color') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors">
                                <span x-show="modalMode === 'create'">Simpan</span>
                                <span x-show="modalMode === 'edit'">Perbarui</span>
                            </button>
                            <button type="button" @click="openCreateModal()" x-show="modalMode === 'edit'" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                                Tambah Baru
                            </button>
                        </div>
                    </form>

                    {{-- List Kategori --}}
                    <div class="p-6 overflow-y-auto bg-gray-50 dark:bg-gray-800/50">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Kategori yang Ada</h4>
                        <div class="space-y-2">
                            @forelse ($broadcastCategories as $category)
                                @php $count = $category->broadcasts()->count(); @endphp
                                <div class="flex items-center justify-between gap-2 p-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->color_classes }} flex-shrink-0">
                                            {{ $category->name }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                            ({{ $count }} item)
                                        </span>
                                    </div>
                                    <div class="flex-shrink-0 flex items-center gap-1">
                                        <button type="button" @click="openEditModal(@js($category))" class="p-1.5 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors" title="Edit" aria-label="Edit kategori siaran">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>

                                        <button type="button" @click="openDeleteCategoryModal(@js($category))"
                                            @if($count === 0)
                                                class="p-1.5 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-500 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                            @else
                                                class="p-1.5 text-gray-300 dark:text-gray-600 cursor-not-allowed" disabled
                                            @endif
                                            aria-label="{{ $count > 0 ? 'Tidak bisa dihapus karena masih digunakan' : 'Hapus Kategori' }}"
                                            title="{{ $count > 0 ? 'Tidak bisa dihapus karena masih digunakan' : 'Hapus Kategori' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-6 text-gray-500 dark:text-gray-400">
                                    <p class="text-sm">Belum ada kategori.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Modal Footer Hapus Kategori --}}
                <div x-show="deleteCategoryModal" class="absolute inset-0 z-10 bg-white/50 dark:bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl max-w-sm w-full border border-gray-200 dark:border-gray-700 p-6">
                         <div class="text-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Hapus Kategori?</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                                Kategori "<span x-text="deleteCategoryName" class="font-medium text-gray-900 dark:text-white"></span>" akan dihapus permanen.
                            </p>
                            <div class="flex justify-center gap-3">
                                <button type="button" @click="closeDeleteCategoryModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">Batal</button>
                                <form :action="@js(route('dashboard.penyiaran-kategori.destroy', ':id')).replace(':id', deleteCategoryId)" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:ring-red-300 transition-colors">Ya, Hapus</button>
                                </form>
                            </div>
                         </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-dashboard-layout>
