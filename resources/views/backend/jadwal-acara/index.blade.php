<x-dashboard-layout>
    <x-slot:title>Kelola Jadwal Acara</x-slot:title>

    <div x-data="{
        // Modal Hapus Jadwal
        deleteModal: false,
        deleteId: null,
        deleteTitle: '',

        // Modal Kategori (Hari)
        categoryModal: false,
        catMode: 'create',
        catModalTitle: 'Tambah Hari Baru',
        catAction: @js(route('dashboard.jadwal-categories.store')),
        catUpdateUrlTemplate: @js(route('dashboard.jadwal-categories.update', ':id')),
        catData: { id: null, name: '', slug: '', color: 'blue', order: 0 },

        // Modal Hapus Kategori
        deleteCatModal: false,
        deleteCatId: null,
        deleteCatName: '',
        deleteCatError: '',

        // Helper Slug
        generateSlug() {
            this.catData.slug = this.catData.name.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
        },

        // --- Functions ---
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

        openCategoryModal(mode, data = null) {
            this.catMode = mode;
            this.categoryModal = true;

            if (mode === 'create') {
                this.catModalTitle = 'Tambah Hari Baru';
                this.catAction = @js(route('dashboard.jadwal-categories.store'));
                this.catData = { id: null, name: '', slug: '', color: 'blue' };
            } else {
                this.catModalTitle = 'Edit Hari';
                this.catAction = this.catUpdateUrlTemplate.replace(':id', data.id);
                this.catData = {
                    id: data.id,
                    name: data.name,
                    slug: data.slug,
                    color: data.color,
                    order: data.order // [BARU] Load data order yang ada
                };
            }
            document.body.style.overflow = 'hidden';
        },

        closeCategoryModal() {
            this.categoryModal = false;
            this.deleteCatModal = false;
            this.deleteCatId = null;
            this.deleteCatName = '';
            this.deleteCatError = '';
            document.body.style.overflow = '';
        },

        openDeleteCatModal(id, name) {
            this.deleteCatId = id;
            this.deleteCatName = name;
            this.deleteCatError = '';
            this.deleteCatModal = true;
            document.body.style.overflow = 'hidden';
        },

        closeDeleteCatModal() {
            this.deleteCatModal = false;
            this.deleteCatId = null;
            this.deleteCatName = '';
            this.deleteCatError = '';
            document.body.style.overflow = this.categoryModal ? 'hidden' : '';
        }
    }"
    class="pb-6"
    @if ($errors->has('name') || $errors->has('slug') || $errors->has('color'))
        x-init="categoryModal = true; document.body.style.overflow = 'hidden';"
    @endif
    >

        {{-- HEADER HALAMAN --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6 pt-2">
            <div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                    Kelola Jadwal Acara
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelola jadwal penayangan program TV.
                </p>
            </div>

            <div class="dashboard-header-actions flex flex-wrap items-center gap-3">
                {{-- Tombol Kelola Hari --}}
                <button @click="openCategoryModal('create')"
                        type="button"
                        class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors whitespace-nowrap shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Kelola Hari
                </button>

                <a href="{{ route('dashboard.jadwal-acara.create') }}"
                   class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors whitespace-nowrap shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd"></path>
                    </svg>
                    Tambah Jadwal
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

        {{-- FORM FILTER & PENCARIAN --}}
        <form method="GET" action="{{ route('dashboard.jadwal-acara.index') }}" class="mb-6">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="relative flex-grow">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           class="block w-full p-3 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                           placeholder="Cari acara...">
                </div>

                <select name="category"
                        class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full md:w-48 p-3 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    <option value="">Semua Hari</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->slug }}" @selected(request('category') == $category->slug)>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <div class="flex gap-2">
                    <button type="submit"
                            class="w-full md:w-auto px-5 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 transition-colors">
                        Cari
                    </button>
                    @if(request()->hasAny(['search', 'category']))
                        <a href="{{ route('dashboard.jadwal-acara.index') }}"
                           class="w-full md:w-auto px-5 py-3 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 text-center transition-colors">
                            Reset
                        </a>
                    @endif
                </div>
            </div>
        </form>

        {{-- INFO HASIL FILTER --}}
        @if(request()->hasAny(['search', 'category']))
            <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
                Menampilkan {{ $jadwalAcaras->total() }} hasil
                @if(request('search'))
                    untuk pencarian <strong class="text-gray-900 dark:text-white">"{{ request('search') }}"</strong>
                @endif
                @if(request('category'))
                    dalam hari <strong class="text-gray-900 dark:text-white">"{{ $categories->where('slug', request('category'))->first()->name ?? request('category') }}"</strong>
                @endif
            </div>
        @endif

        {{-- TABEL DATA --}}
        <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="dashboard-data-table w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-center w-32">Pukul</th>
                            <th scope="col" class="px-4 py-3 text-left">Nama Program</th>
                            <th scope="col" class="px-4 py-3 text-center">Hari</th>
                            <th scope="col" class="px-4 py-3 text-center">Jenis</th>
                            <th scope="col" class="px-4 py-3 text-center w-28">Status</th>
                            <th scope="col" class="px-4 py-3 text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($jadwalAcaras as $jadwal)
                            <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                {{-- Kolom Waktu --}}
                                <td class="px-4 py-4 align-middle text-center">
                                    <div class="font-mono text-sm sm:text-base font-bold text-blue-600 dark:text-blue-400 whitespace-nowrap">
                                        {{ $jadwal->start_time->format('H:i') }}
                                        <span aria-hidden="true">–</span>
                                        {{ $jadwal->end_time?->format('H:i') ?? $jadwal->start_time->copy()->addHour()->format('H:i') }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">WIB</div>
                                </td>

                                {{-- Kolom Nama Program --}}
                                <td class="px-4 py-4 align-middle">
                                    <div class="space-y-1">
                                        <p class="font-semibold text-gray-900 dark:text-white line-clamp-2 leading-tight">
                                            {{ $jadwal->title }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 font-mono hidden sm:block">
                                            /{{ $jadwal->slug }}
                                        </p>
                                    </div>
                                </td>

                                {{-- Kolom Hari --}}
                                <td class="px-4 py-4 align-middle text-center">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $jadwal->jadwalCategory->color_classes ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300' }}">
                                        {{ $jadwal->jadwalCategory->name ?? '-' }}
                                    </span>
                                </td>

                                {{-- Kolom Jenis --}}
                                <td class="px-4 py-4 align-middle text-center">
                                    <span class="text-xs text-gray-600 dark:text-gray-400">
                                        {{ $jadwal->broadcastCategory->name ?? '-' }}
                                    </span>
                                </td>

                                {{-- Kolom Status --}}
                                <td class="px-4 py-4 align-middle text-center">
                                    @if($jadwal->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/80 dark:text-green-300 border border-green-200 dark:border-green-800">
                                            Tayang
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400 border border-gray-200 dark:border-gray-600">
                                            Non-Aktif
                                        </span>
                                    @endif
                                </td>

                                {{-- Kolom Aksi --}}
                                <td class="px-4 py-4 align-middle text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- PERBAIKAN: Menambahkan '}}' di akhir href --}}
                                        <a href="{{ route('dashboard.jadwal-acara.edit', $jadwal) }}"
                                        class="p-2 text-yellow-600 bg-yellow-50 rounded-lg hover:bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-900/30 dark:hover:bg-yellow-900/50 transition-colors"
                                        title="Edit" aria-label="Edit jadwal acara">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>

                                        <button type="button" @click="openDeleteModal({{ $jadwal->id }}, @js($jadwal->title))"
                                                class="p-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 dark:text-red-400 dark:bg-red-900/30 dark:hover:bg-red-900/50 transition-colors"
                                                title="Hapus" aria-label="Hapus jadwal acara">
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <p class="text-base font-medium">Tidak ada jadwal acara ditemukan</p>
                                        @if(request()->hasAny(['search', 'category']))
                                            <p class="text-sm text-gray-400 dark:text-gray-500 mt-1">Coba kata kunci lain atau reset filter</p>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- PAGINASI --}}
        @if($jadwalAcaras->hasPages())
            <div class="mt-6">
                {{ $jadwalAcaras->links() }}
            </div>
        @endif

        {{-- MODAL KELOLA HARI --}}
        <div x-show="categoryModal"
             x-cloak
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 transform scale-95"
             x-transition:enter-end="opacity-100 transform scale-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 transform scale-100"
             x-transition:leave-end="opacity-0 transform scale-95"
             class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/75 backdrop-blur-sm flex items-center justify-center p-4"
             @keydown.escape.window="deleteCatModal ? closeDeleteCatModal() : closeCategoryModal()">

            <div class="relative bg-white rounded-lg shadow-xl dark:bg-gray-800 max-w-2xl w-full max-h-[90vh] overflow-hidden flex flex-col"
                 @click.away="closeCategoryModal()">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-5 border-b dark:border-gray-700 flex-shrink-0 bg-gray-50 dark:bg-gray-800">
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white" x-text="catModalTitle"></h3>
                    <button @click="closeCategoryModal()"
                            type="button"
                            class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                    </button>
                </div>

                {{-- Modal Body --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-0 overflow-hidden h-full">

                    {{-- Form Input --}}
                    <form :action="catAction"
                          method="POST"
                          @submit="if(catMode === 'edit') { $event.target.querySelector('[name=_method]').value = 'PUT'; }"
                          class="p-6 border-r dark:border-gray-700 overflow-y-auto">
                        @csrf
                        <input type="hidden" name="_method" value="POST">

                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4" x-text="catMode === 'edit' ? 'Edit Hari' : 'Tambah Hari Baru'"></h4>

                        {{-- Nama Hari --}}
                        <div class="mb-4">
                            <label for="cat-name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Nama Hari <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="cat-name"
                                   name="name"
                                   x-model="catData.name"
                                   @input="generateSlug()"
                                   class="bg-gray-50 border text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white {{ $errors->has('name') ? 'border-red-500' : 'border-gray-300' }}"
                                   placeholder="Contoh: Senin - Jumat"
                                   required>
                            @error('name')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Slug --}}
                        <div class="mb-4">
                            <label for="cat-slug" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Slug <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   id="cat-slug"
                                   name="slug"
                                   x-model="catData.slug"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white font-mono text-xs {{ $errors->has('slug') ? 'border-red-500' : 'border-gray-300' }}"
                                   placeholder="senin-jumat"
                                   required>
                            @error('slug')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- [BARU] Input Urutan --}}
                        <div class="mb-4">
                            <label for="cat-order" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Urutan Tampil
                            </label>
                            <input type="number"
                                id="cat-order"
                                name="order"
                                x-model="catData.order"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="1"
                                required>
                            <p class="mt-1 text-xs text-gray-500">Angka kecil (1) tampil duluan. Contoh: Senin=1, Selasa=2.</p>
                        </div>

                        {{-- Warna --}}
                        <div class="mb-4">
                            <label for="cat-color" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Warna Badge
                            </label>
                            <select id="cat-color"
                                    name="color"
                                    x-model="catData.color"
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
                            @error('color')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <button type="submit"
                                    class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors">
                                <span x-show="catMode === 'create'">Simpan Hari</span>
                                <span x-show="catMode === 'edit'">Perbarui Hari</span>
                            </button>
                            <button type="button"
                                    @click="openCategoryModal('create')"
                                    x-show="catMode === 'edit'"
                                    class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                                Tambah Baru
                            </button>
                        </div>
                    </form>

                    {{-- List Kategori --}}
                    <div class="p-6 overflow-y-auto bg-gray-50 dark:bg-gray-800/50">
                        <h4 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Hari yang Tersedia</h4>
                        <div class="space-y-2">
                            @forelse($jadwalCategories as $cat)
                                @php
                                    $jadwalCount = $cat->jadwalAcaras()->count();
                                @endphp
                                <div class="flex items-center justify-between gap-2 p-3 rounded-lg hover:bg-white dark:hover:bg-gray-700/50 border border-gray-200 dark:border-gray-600 transition-colors bg-white dark:bg-gray-800">
                                    <div class="flex items-center gap-3 flex-1 min-w-0">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cat->color_classes }} flex-shrink-0">
                                            {{ $cat->name }}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 truncate">
                                            ({{ $jadwalCount }} jadwal)
                                        </span>
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="flex-shrink-0 flex items-center gap-1">
                                        {{-- Tombol Edit --}}
                                        <button type="button" @click="openCategoryModal('edit', @js($cat))"
                                                class="p-1.5 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors rounded hover:bg-blue-50 dark:hover:bg-blue-900/20"
                                                title="Edit Hari" aria-label="Edit Hari">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>

                                        {{-- Tombol Hapus --}}
                                        <button
                                            type="button"
                                            @click="openDeleteCatModal({{ $cat->id }}, @js($cat->name))"
                                            @if($jadwalCount === 0)
                                            class="p-1.5 rounded transition-colors text-gray-500 hover:text-red-600 hover:bg-red-50 dark:text-gray-400 dark:hover:text-red-500 dark:hover:bg-red-900/20"
                                            @else
                                            class="p-1.5 rounded transition-colors text-gray-300 dark:text-gray-600 opacity-50 cursor-not-allowed"
                                            disabled
                                            @endif
                                            aria-label="{{ $jadwalCount > 0 ? 'Hari tidak bisa dihapus karena memiliki jadwal' : 'Hapus Hari' }}"
                                            title="{{ $jadwalCount > 0 ? 'Hari tidak bisa dihapus karena memiliki jadwal' : 'Hapus Hari' }}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-sm">Belum ada hari.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- MODAL HAPUS HARI --}}
        <div x-show="deleteCatModal"
            x-cloak
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="fixed inset-0 z-[60] overflow-y-auto bg-gray-900/75 backdrop-blur-sm flex items-center justify-center p-4"
            @keydown.escape.window="closeDeleteCatModal()">

            <div class="relative bg-white rounded-lg shadow-xl dark:bg-gray-800 max-w-md w-full"
                @click.away="closeDeleteCatModal()">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-4 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Konfirmasi Hapus Hari</h3>
                    <button @click="closeDeleteCatModal()" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm p-1.5 ml-auto inline-flex items-center dark:hover:bg-gray-700 dark:hover:text-white">
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
                                Hari <span class="font-medium text-gray-900 dark:text-white" x-text="deleteCatName"></span> akan dihapus permanen.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-3 p-4 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <button @click="closeDeleteCatModal()" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700 transition-colors">
                        Batal
                    </button>

                    <form :action="@js(route('dashboard.jadwal-categories.destroy', ':id')).replace(':id', deleteCatId)" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800 transition-colors">
                            Hapus Hari
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- MODAL HAPUS JADWAL --}}
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

            <div class="relative bg-white rounded-lg shadow-xl dark:bg-gray-800 max-w-md w-full"
                @click.away="closeDeleteModal()">

                {{-- Modal Header --}}
                <div class="flex items-center justify-between p-4 border-b dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
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
                            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-2">
                                Jadwal <span class="font-medium text-gray-900 dark:text-white" x-text="deleteTitle"></span> akan dihapus permanen.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-3 p-4 border-t dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <button @click="closeDeleteModal()" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700 transition-colors">
                        Batal
                    </button>

                    <form :action="@js(route('dashboard.jadwal-acara.destroy', ':id')).replace(':id', deleteId)" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800 transition-colors">
                            Ya, Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>
