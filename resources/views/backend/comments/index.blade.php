<x-dashboard-layout>
    <x-slot:title>Kelola Komentar</x-slot:title>

    <div x-data="{
        deleteModal: false,
        deleteItemId: null,
        deleteItemName: '',

        openDeleteModal(itemId, itemName) {
            this.deleteItemId = itemId;
            this.deleteItemName = itemName;
            this.deleteModal = true;
            document.body.style.overflow = 'hidden';
        },

        closeDeleteModal() {
            this.deleteModal = false;
            this.deleteItemId = null;
            this.deleteItemName = '';
            document.body.style.overflow = '';
        }
    }"
    class="pb-6">

        {{-- HEADER HALAMAN --}}
        <div class="flex flex-col gap-4 mb-6 pt-2">
            <div>
                <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                    Kelola Komentar
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelola, setujui, dan hapus komentar publik yang masuk.
                </p>
            </div>

            {{-- Status Filter Tabs --}}
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('dashboard.komentar.index') }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full transition-colors
                {{ !request('status') ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                    Semua
                    <span class="inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold rounded-full bg-white/50 dark:bg-gray-800/50">{{ $counts['all'] }}</span>
                </a>
                <a href="{{ route('dashboard.komentar.index', ['status' => 'pending']) }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full transition-colors
                {{ request('status') == 'pending' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/50 dark:text-yellow-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                    Pending
                    <span class="inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold rounded-full bg-white/50 dark:bg-gray-800/50">{{ $counts['pending'] }}</span>
                </a>
                <a href="{{ route('dashboard.komentar.index', ['status' => 'approved']) }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full transition-colors
                {{ request('status') == 'approved' ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                    Disetujui
                    <span class="inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold rounded-full bg-white/50 dark:bg-gray-800/50">{{ $counts['approved'] }}</span>
                </a>
                <a href="{{ route('dashboard.komentar.index', ['status' => 'rejected']) }}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium rounded-full transition-colors
                {{ request('status') == 'rejected' ? 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-700' : 'bg-gray-100 text-gray-600 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600' }}">
                    Ditolak
                    <span class="inline-flex items-center justify-center w-5 h-5 text-[10px] font-bold rounded-full bg-white/50 dark:bg-gray-800/50">{{ $counts['rejected'] }}</span>
                </a>
            </div>

            {{-- Pencarian --}}
            <div class="flex flex-col sm:flex-row gap-3 w-full items-center">
                <form action="{{ route('dashboard.komentar.index') }}" method="GET" class="flex items-center w-full gap-2">
                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    <label for="simple-search" class="sr-only">Cari komentar</label>
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}" id="simple-search"
                            class="bg-white border border-gray-200 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 p-2.5 shadow-sm dark:bg-gray-800 dark:border-gray-700 dark:placeholder-gray-400 dark:text-white transition-colors"
                            placeholder="Cari nama, email, isi...">
                    </div>
                    <button type="submit" class="p-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 shadow-sm transition-colors flex-shrink-0" title="Cari" aria-label="Cari komentar">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <span class="sr-only">Cari</span>
                    </button>

                    @if(request('search'))
                        <a href="{{ route('dashboard.komentar.index', request()->only('status')) }}" class="p-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-lg hover:bg-gray-50 focus:ring-4 focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-700 dark:hover:bg-gray-700 shadow-sm transition-colors flex-shrink-0" title="Reset Pencarian" aria-label="Reset Pencarian">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- ALERT SUCCESS --}}
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
        @endif

        {{-- ALERT ERROR --}}
        @if($errors->has('error'))
            <div class="mb-6 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800 transition-all duration-300" role="alert">
                <div class="flex items-center">
                    <svg class="flex-shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <div>
                        <span class="font-medium">Gagal!</span> {{ $errors->first('error') }}
                    </div>
                </div>
            </div>
        @endif

        {{-- TABEL DATA --}}
        <div class="bg-white dark:bg-gray-800 shadow-md sm:rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="dashboard-data-table w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-300">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left min-w-[200px]">Pengirim</th>
                            <th scope="col" class="px-4 py-3 text-left min-w-[300px]">Komentar</th>
                            <th scope="col" class="px-4 py-3 text-left min-w-[150px]">Di Konten</th>
                            <th scope="col" class="px-4 py-3 text-center w-24">Status</th>
                            <th scope="col" class="px-4 py-3 text-center w-40">Waktu</th>
                            <th scope="col" class="px-4 py-3 text-center w-32">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @forelse ($comments as $comment)
                            <tr class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">

                                {{-- Pengirim --}}
                                <td class="px-4 py-4 align-middle">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 flex items-center justify-center font-bold text-sm ring-2 ring-gray-200 dark:ring-gray-700">
                                                {{ strtoupper(substr($comment->name ?? '?', 0, 1)) }}
                                            </div>
                                        </div>
                                        <div class="flex flex-col gap-1">
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $comment->name }}</span>
                                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $comment->email }}</span>
                                        </div>
                                    </div>
                                </td>

                                {{-- Komentar --}}
                                <td class="px-4 py-4 align-middle">
                                    @if($comment->rating)
                                        <div class="text-yellow-400 text-xs mb-1.5 flex items-center" title="Nilai: {{ $comment->rating }}/5">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $comment->rating)
                                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                @else
                                                    <svg class="w-3.5 h-3.5 text-gray-300 dark:text-gray-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                                @endif
                                            @endfor
                                        </div>
                                    @endif
                                    <div class="line-clamp-2 text-sm text-gray-600 dark:text-gray-300" title="{{ $comment->body }}">{{ $comment->body }}</div>
                                </td>

                                {{-- Di Konten --}}
                                <td class="px-4 py-4 align-middle">
                                    @if($comment->commentable)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300 mb-1 w-fit">
                                            {{ class_basename($comment->commentable_type) }}
                                        </span>
                                        <div class="truncate max-w-[150px] font-medium text-xs text-gray-900 dark:text-gray-200" title="{{ $comment->commentable->title }}">
                                            {{ $comment->commentable->title }}
                                        </div>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300 w-fit">
                                            Data Terhapus
                                        </span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-4 align-middle text-center">
                                    @if($comment->status === 'pending')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-800 border border-yellow-200 dark:border-yellow-700">
                                            <span class="w-1.5 h-1.5 bg-yellow-500 rounded-full mr-1.5"></span>
                                            Pending
                                        </span>
                                    @elseif($comment->status === 'approved')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300 border border-green-200 dark:border-green-700">
                                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>
                                            Disetujui
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-800 border border-red-200 dark:border-red-700">
                                            <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>
                                            Ditolak
                                        </span>
                                    @endif
                                </td>

                                {{-- Tanggal --}}
                                <td class="px-4 py-4 align-middle text-center">
                                    <span class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $comment->created_at ? $comment->created_at->format('d M Y') : '-' }}
                                        <br>
                                        {{ $comment->created_at ? $comment->created_at->format('H:i') : '' }}
                                    </span>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-4 py-4 align-middle text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        @if($comment->status !== 'approved')
                                            <form action="{{ route('dashboard.komentar.setujui', $comment) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="p-2 text-green-600 bg-green-50 rounded-lg hover:bg-green-100 dark:text-green-400 dark:bg-green-900/30 dark:hover:bg-green-900/50 transition-colors" title="Setujui Komentar" aria-label="Setujui Komentar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                </button>
                                            </form>
                                        @endif
                                        @if($comment->status !== 'rejected')
                                            <form action="{{ route('dashboard.komentar.tolak', $comment) }}" method="POST" class="inline">
                                                @csrf
                                                @method('PUT')
                                                <button type="submit" class="p-2 text-red-600 bg-red-50 rounded-lg hover:bg-red-100 dark:text-red-400 dark:bg-red-900/30 dark:hover:bg-red-900/50 transition-colors" title="Tolak Komentar" aria-label="Tolak Komentar">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                            </form>
                                        @endif
                                        <button type="button" @click="openDeleteModal({{ $comment->id }}, @js($comment->name))"
                                            class="p-2 text-gray-600 bg-gray-50 rounded-lg hover:bg-gray-100 dark:text-gray-400 dark:bg-gray-900/30 dark:hover:bg-gray-900/50 transition-colors"
                                            title="Hapus Komentar" aria-label="Hapus Komentar">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="w-12 h-12 mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                                        <p class="text-base font-medium">Belum ada komentar sama sekali.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($comments->hasPages())
                <div class="mt-4 mb-4 px-4 sm:px-6">
                    <div class="bg-gray-50 dark:bg-gray-800/50 rounded-2xl p-4">
                        {{ $comments->withQueryString()->links('vendor.pagination.tailwind') }}
                    </div>
                </div>
            @endif
        </div>

        {{-- MODAL HAPUS (Alpine.js) --}}
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
                            <h4 class="text-base font-semibold text-gray-900 dark:text-white mb-1">Hapus Komentar?</h4>
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Anda yakin ingin menghapus komentar dari pengirim <strong class="text-gray-900 dark:text-white" x-text="deleteItemName"></strong>? Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="flex items-center justify-end gap-3 p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                    <button @click="closeDeleteModal()" type="button" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:ring-4 focus:outline-none focus:ring-gray-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700 transition-colors">
                        Batal
                    </button>

                    <form :action="@js(route('dashboard.komentar.destroy', ':id')).replace(':id', deleteItemId)" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 focus:ring-4 focus:outline-none focus:ring-red-300 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-800 transition-colors">
                            Hapus Komentar
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</x-dashboard-layout>
