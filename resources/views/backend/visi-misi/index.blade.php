<x-dashboard-layout>
    <x-slot:title>Kelola Visi & Misi</x-slot:title>

    {{-- Data Alpine.js untuk Modal Hapus --}}
    <div x-data="{
        deleteModal: false,
        deleteItemId: null,
        deleteItemContent: '',

        openDeleteModal(itemId, itemContent) {
            this.deleteItemId = itemId;
            this.deleteItemContent = itemContent;
            this.deleteModal = true;
            document.body.style.overflow = 'hidden';
        },

        closeDeleteModal() {
            this.deleteModal = false;
            this.deleteItemId = null;
            this.deleteItemContent = '';
            document.body.style.overflow = '';
        }
    }"
    class="pb-6">

        {{-- HEADER HALAMAN --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-center gap-4 mb-6 pt-2">
            <div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                    Kelola Visi & Misi
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelola visi dan poin-poin misi organisasi.
                </p>
            </div>

            <a href="{{ route('dashboard.visi-misi.create') }}"
               class="w-full md:w-auto inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 transition-colors whitespace-nowrap shadow-sm hover:shadow-md">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Visi/Misi
            </a>
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

        {{-- TABEL DATA --}}
        <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="dashboard-data-table w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-center w-12">No</th>
                            <th scope="col" class="px-4 py-3 text-center w-20">Urut</th>
                            <th scope="col" class="px-4 py-3 text-center w-28">Gambar</th>
                            <th scope="col" class="px-4 py-3 text-center w-24">Tipe</th>
                            <th scope="col" class="px-4 py-3 min-w-[200px]">Isi Konten</th>
                            <th scope="col" class="px-4 py-3 text-center w-32">Status</th>
                            <th scope="col" class="px-4 py-3 text-center w-36">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($visiMisis as $item)
                            <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <td class="px-4 py-4 text-center font-medium text-gray-900 dark:text-white">
                                    {{ $loop->iteration }}
                                </td>

                                {{-- Urutan --}}
                                <td class="px-4 py-4 text-center">
                                    <span class="inline-flex items-center justify-center w-8 h-8 text-xs font-bold bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 rounded-full border border-gray-200 dark:border-gray-600">
                                        {{ $item->order }}
                                    </span>
                                </td>

                                {{-- Gambar --}}
                                <td class="px-4 py-4 text-center">
                                    @if ($item->image)
                                        <img src="{{ Storage::url($item->image) }}"
                                             alt="Gambar"
                                             class="w-16 h-12 object-cover rounded-lg ring-1 ring-gray-200 dark:ring-gray-700 inline-block">
                                    @else
                                        <div class="w-16 h-12 inline-flex items-center justify-center bg-gray-100 dark:bg-gray-700 rounded-lg ring-1 ring-gray-200 dark:ring-gray-700">
                                            <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </td>

                                {{-- Tipe --}}
                                <td class="px-4 py-4 text-center">
                                    @if($item->type === 'visi')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300 border border-purple-200 dark:border-purple-800 uppercase tracking-wide">
                                            Visi
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300 border border-blue-200 dark:border-blue-800 uppercase tracking-wide">
                                            Misi
                                        </span>
                                    @endif
                                </td>

                                {{-- Isi Konten --}}
                                <td class="px-4 py-4">
                                    <div class="line-clamp-2 text-sm text-gray-600 dark:text-gray-300">
                                        {{ strip_tags($item->content) }}
                                    </div>
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-4 text-center">
                                    @if($item->is_active)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/80 dark:text-green-300 border border-green-200 dark:border-green-800">
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-600 dark:bg-gray-700/80 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                            Non-Aktif
                                        </span>
                                    @endif
                                </td>

                                {{-- Aksi --}}
                                <td class="px-4 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('dashboard.visi-misi.edit', $item->id) }}"
                                           class="p-2 text-yellow-600 bg-yellow-50 rounded-lg hover:bg-yellow-100 dark:text-yellow-400 dark:bg-yellow-900/30 dark:hover:bg-yellow-900/50 transition-colors"
                                            title="Edit" aria-label="Edit visi dan misi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </a>

                                        <button type="button" @click="openDeleteModal({{ $item->id }}, @js(Str::limit(strip_tags($item->content), 50)))"
                                                class="p-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 dark:text-red-400 dark:bg-red-900/30 dark:hover:bg-red-900/50 transition-colors"
                                            title="Hapus" aria-label="Hapus visi dan misi">
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
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="text-base font-medium">Belum ada data Visi & Misi</p>
                                        <p class="text-sm mt-1">Silakan tambahkan data baru untuk ditampilkan.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- MODAL HAPUS --}}
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

            <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full overflow-hidden"
                 @click.away="closeDeleteModal()">

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
                            <p class="text-sm text-gray-500 dark:text-gray-400 line-clamp-3">
                                Data <span class="font-medium text-gray-900 dark:text-white" x-text="deleteItemContent"></span> akan dihapus permanen. Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <button @click="closeDeleteModal()" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700 transition-colors">
                        Batal
                    </button>

                    <form :action="@js(route('dashboard.visi-misi.destroy', ':id')).replace(':id', deleteItemId)" method="POST">
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
