<x-dashboard-layout>
    <x-slot:title>Kelola Berita</x-slot:title>

    {{-- Data Alpine.js Lengkap (Gabungan Hapus Berita & CRUD Kategori) --}}
    <div x-data="{ 
        // --- Logic Hapus Berita ---
        deleteModal: false, 
        deleteId: null, 
        deleteTitle: '',

        // --- Logic Modal Kategori ---
        isCategoryModalOpen: false,
        modalMode: 'create',
        modalTitle: 'Tambah Kategori Berita',
        modalAction: '{{ route('dashboard.kategori-berita.store') }}',
        updateUrlTemplate: '{{ route('dashboard.kategori-berita.update', ':id') }}',
        
        categoryData: {
            id: null,
            name: '',
            slug: '',
            color: 'blue' // Default
        },

        // --- Logic Hapus Kategori ---
        deleteCategoryModal: false,
        deleteCategoryId: null,
        deleteCategoryName: '',

        // Helper Slug Generator
        generateSlug() {
            this.categoryData.slug = this.categoryData.name
                .toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
        },
        
        // Functions Berita
        openDeleteModal(id, title) {
            this.deleteId = id;
            this.deleteTitle = title;
            this.deleteModal = true;
            document.body.style.overflow = 'hidden';
        },
        closeDeleteModal() {
            this.deleteModal = false;
            document.body.style.overflow = '';
        },

        // Functions Kategori
        openCreateCategoryModal() {
            this.modalMode = 'create';
            this.modalTitle = 'Tambah Kategori Berita';
            this.modalAction = '{{ route('dashboard.kategori-berita.store') }}';
            this.categoryData = { id: null, name: '', slug: '', color: 'blue' };
            this.isCategoryModalOpen = true;
            document.body.style.overflow = 'hidden';
        },

        openEditCategoryModal(category) {
            this.modalMode = 'edit';
            this.modalTitle = 'Edit Kategori Berita';
            // Replace placeholder ID dengan ID kategori yang diklik
            this.modalAction = this.updateUrlTemplate.replace(':id', category.id);
            this.categoryData = {
                id: category.id,
                name: category.name,
                slug: category.slug,
                color: category.color || 'blue' 
            };
            this.isCategoryModalOpen = true;
            document.body.style.overflow = 'hidden';
        },

        closeCategoryModal() {
            this.isCategoryModalOpen = false;
            document.body.style.overflow = '';
        },

        openDeleteCategoryModal(category) {
            this.deleteCategoryId = category.id;
            this.deleteCategoryName = category.name;
            this.deleteCategoryModal = true;
            document.body.style.overflow = 'hidden';
        },

        closeDeleteCategoryModal() {
            this.deleteCategoryModal = false;
            document.body.style.overflow = '';
        }
    }" 
    class="pb-6"
    {{-- Buka modal otomatis jika ada error validasi kategori --}}
    @if ($errors->has('name') || $errors->has('slug'))
        x-init="isCategoryModalOpen = true; document.body.style.overflow = 'hidden';"
    @endif
    >
        
        {{-- HEADER HALAMAN --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6 pt-2">
            <div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                    Kelola Berita
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelola berita terkini dan kategori berita.
                </p>
            </div>
            
            <div class="dashboard-header-actions flex flex-wrap items-center gap-3">
                {{-- Tombol Kelola Kategori (Sekarang Aktif) --}}
                <button @click="openCreateCategoryModal()" 
                        type="button" 
                        class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors whitespace-nowrap shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7A2 2 0 0121 12v5a2 2 0 01-2 2H7a2 2 0 01-2-2V5a2 2 0 012-2h3m0 0l-3 3m0 0l3 3m-3-3h5"></path>
                    </svg>
                    Kelola Kategori
                </button>

                {{-- Tombol Buat Berita --}}
                <a href="{{ route('dashboard.berita.create') }}" 
                   class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors whitespace-nowrap shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                    </svg>
                    Tambah Berita
                </a>
            </div>
        </div>

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
        
        {{-- ALERT ERROR --}}
        @if(session('error'))
            <div class="mb-6 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800" role="alert">
                <span class="font-medium">Gagal!</span> {{ session('error') }}
            </div>
        @endif

        {{-- FORM FILTER --}}
        <form method="GET" action="{{ route('dashboard.berita.index') }}" class="mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path></svg>
                    </div>
                    <input type="text" name="search" value="{{ request('search') }}" class="block w-full p-3 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Cari berita...">
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
                    <button type="submit" class="w-full md:w-auto px-5 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-colors">Cari</button>
                    @if(request()->hasAny(['search', 'category', 'sort']))
                        <a href="{{ route('dashboard.berita.index') }}" class="w-full md:w-auto px-5 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 text-center transition-colors">Reset</a>
                    @endif
                </div>
            </div>
        </form>

        {{-- TABEL BERITA --}}
        <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="dashboard-data-table w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-center w-12">No</th>
                            <th scope="col" class="px-4 py-3 text-left">Judul & Kategori</th>
                            <th scope="col" class="px-4 py-3 text-center w-24">Link</th>
                            <th scope="col" class="px-4 py-3 text-center w-28">Gambar</th>
                            <th scope="col" class="px-4 py-3 text-center w-36">Penulis</th>
                            <th scope="col" class="px-4 py-3 text-center w-32">Tanggal</th>
                            <th scope="col" class="px-4 py-3 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($posts as $item) 
                            <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-4 py-4 align-top text-center font-medium text-gray-900 dark:text-white">
                                    {{ $loop->iteration + $posts->firstItem() - 1 }}
                                </td>
                                
                                <td class="px-4 py-4 align-top">
                                    <div class="space-y-2">
                                        <p class="font-semibold text-gray-900 dark:text-white line-clamp-2 leading-tight">{{ $item->title }}</p>
                                        <div class="flex flex-wrap gap-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->newsCategory?->colorClasses ?? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' }}">
                                                {{ $item->newsCategory->name ?? 'Tanpa Kategori' }}
                                            </span>
                                            @if($item->status === 'draft')
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300">
                                                    Draf
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>

                                <td class="px-4 py-4 align-top text-center">
                                    @if ($item->link_berita)
                                        <a href="{{ $item->link_berita }}" target="_blank" rel="noopener noreferrer"
                                            class="inline-flex items-center justify-center p-2 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/50 rounded-lg transition-colors group">
                                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                            </svg>
                                        </a>
                                    @else
                                        <span class="text-gray-400 dark:text-gray-600 text-sm">-</span>
                                    @endif
                                </td>

                                <td class="px-4 py-4 align-top text-center">
                                    @if ($item->featured_image)
                                        <img src="{{ Storage::url($item->featured_image) }}" alt="Gambar" class="w-16 h-12 object-cover rounded-lg ring-1 ring-gray-200 dark:ring-gray-700 inline-block">
                                    @else
                                        <div class="w-16 h-12 inline-flex items-center justify-center bg-gray-100 dark:bg-gray-700 rounded-lg ring-1 ring-gray-200 dark:ring-gray-700">
                                            <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </td>

                                <td class="px-4 py-4 align-top text-center">
                                    <div class="flex flex-col items-center justify-center gap-1.5">
                                        <img class="w-8 h-8 rounded-full ring-2 ring-gray-200 dark:ring-gray-700" src="https://ui-avatars.com/api/?name={{ urlencode($item->user->name) }}&background=random&color=fff" alt="{{ $item->user->name }}">
                                        <span class="text-xs font-medium text-gray-900 dark:text-white truncate max-w-[100px]">{{ Str::limit($item->user->name, 12) }}</span>
                                    </div>
                                </td>

                                <td class="px-4 py-4 align-top text-center">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->created_at->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $item->created_at->format('H:i') }} WIB</div>
                                </td>

                                <td class="px-4 py-4 align-top text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('berita.show', $item->slug) }}" target="_blank" 
                                           class="p-2 text-blue-600 bg-blue-50 rounded-lg hover:bg-blue-100 dark:text-blue-400 dark:bg-blue-900/20 dark:hover:bg-blue-900/50 transition-colors" title="Lihat">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </a>

                                        <a href="{{ route('dashboard.berita.edit', $item) }}" 
                                           class="p-2 text-yellow-600 bg-yellow-50 rounded-lg hover:bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-900/30 dark:hover:bg-yellow-900/50 transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>

                                        <button @click.prevent="openDeleteModal({{ $item->id }}, '{{ addslashes($item->title) }}')" 
                                                class="p-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 dark:text-red-400 dark:bg-red-900/30 dark:hover:bg-red-900/50 transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"></path>
                                        </svg>
                                        <p class="text-base font-medium">Tidak ada berita ditemukan</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($posts->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>

        {{-- MODAL HAPUS BERITA --}}
        <div x-show="deleteModal" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/75 backdrop-blur-sm flex items-center justify-center p-4">
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full overflow-hidden" @click.away="closeDeleteModal()">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Konfirmasi Hapus</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                        Berita <span class="font-medium text-gray-900 dark:text-white" x-text="deleteTitle"></span> akan dihapus permanen.
                    </p>
                    <div class="flex justify-end gap-3">
                        <button @click="closeDeleteModal()" class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Batal</button>
                        <form :action="`{{ route('dashboard.berita.destroy', '') }}/${deleteId}`" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-4 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700 transition">Ya, Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL KELOLA KATEGORI (CREATE/EDIT) --}}
        <div x-show="isCategoryModalOpen" 
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/75 backdrop-blur-sm flex items-center justify-center p-4"
             @keydown.escape.window="closeCategoryModal()">
            
            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col" @click.away="closeCategoryModal()">
                
                {{-- Header --}}
                <div class="flex items-center justify-between p-4 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="modalTitle"></h3>
                    <button @click="closeCategoryModal()" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-0 overflow-hidden h-full">
                    
                    {{-- Form --}}
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
                                placeholder="Contoh: Politik" required>
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

                        {{-- Warna (Opsional, jika didukung DB) --}}
                        <div class="mb-4">
                            <label for="cat-color" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Warna Badge (Opsional)
                            </label>
                            <select id="cat-color" name="color" x-model="categoryData.color"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                <option value="blue">Biru</option>
                                <option value="red">Merah</option>
                                <option value="green">Hijau</option>
                                <option value="yellow">Kuning</option>
                                <option value="purple">Ungu</option>
                                <option value="gray">Abu-abu</option>
                            </select>
                        </div>
                        
                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors">
                                <span x-show="modalMode === 'create'">Simpan</span>
                                <span x-show="modalMode === 'edit'">Perbarui</span>
                            </button>
                            <button type="button" @click="openCreateCategoryModal()" x-show="modalMode === 'edit'" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                                Tambah Baru
                            </button>
                        </div>
                    </form>

                    {{-- List Kategori Existing --}}
                    <div class="p-6 overflow-y-auto bg-gray-50 dark:bg-gray-800/50">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Kategori yang Ada</h4>
                        <div class="space-y-2">
                            @forelse ($categories as $category)
                                @php $count = $category->news()->count(); @endphp
                                <div class="flex items-center justify-between gap-2 p-3 rounded-lg border border-gray-200 dark:border-gray-600 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->colorClasses }} flex-shrink-0">
                                            {{ $category->name }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                            ({{ $count }} berita)
                                        </span>
                                    </div>
                                    <div class="flex-shrink-0 flex items-center gap-1">
                                        <button @click="openEditCategoryModal({{ $category }})" class="p-1.5 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 rounded hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        
                                        <button type="button" @click="openDeleteCategoryModal({{ $category }})"
                                            @if($count === 0)
                                                class="p-1.5 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-500 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                            @else
                                                class="p-1.5 text-gray-300 dark:text-gray-600 cursor-not-allowed" disabled
                                            @endif
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

                {{-- Modal Confirm Hapus Kategori --}}
                <div x-show="deleteCategoryModal" class="absolute inset-0 z-10 bg-white/50 dark:bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl max-w-sm w-full border border-gray-200 dark:border-gray-700 p-6">
                         <div class="text-center">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Hapus Kategori?</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                                Kategori "<span x-text="deleteCategoryName" class="font-medium text-gray-900 dark:text-white"></span>" akan dihapus permanen.
                            </p>
                            <div class="flex justify-center gap-3">
                                <button @click="closeDeleteCategoryModal()" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">Batal</button>
                                <form :action="`{{ route('dashboard.kategori-berita.destroy', '') }}/${deleteCategoryId}`" method="POST">
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
