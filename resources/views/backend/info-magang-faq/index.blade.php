<x-dashboard-layout>
    <x-slot:title>Kelola FAQ Magang</x-slot:title>

    <div class="pb-6">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-8 pt-2">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                    Kelola FAQ Magang
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Kelola daftar pertanyaan dan jawaban seputar program magang.
                </p>
            </div>
            
            <a href="{{ route('dashboard.info-magang-faq.create') }}" 
               class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 transition-colors shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah FAQ
            </a>
        </div>

        {{-- Alert Sukses
        @if(session('success'))
            <div class="mb-6 p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800" role="alert">
                <span class="font-medium">Sukses!</span> {{ session('success') }}
            </div>
        @endif --}}

        {{-- Tabel Data --}}
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="dashboard-data-table w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3 w-16 text-center">Urutan</th>
                            <th scope="col" class="px-6 py-3">Pertanyaan & Jawaban</th>
                            <th scope="col" class="px-6 py-3 w-32 text-center">Status</th>
                            <th scope="col" class="px-6 py-3 w-48 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($faqs as $faq)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-bold text-xs">
                                    {{ $faq->order }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="mb-1">
                                    <span class="text-base font-bold text-gray-900 dark:text-white block mb-1">
                                        {{ $faq->question }}
                                    </span>
                                </div>
                                <p class="text-gray-500 dark:text-gray-400 line-clamp-2 text-xs leading-relaxed">
                                    {{ $faq->answer }}
                                </p>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($faq->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300 border border-green-200 dark:border-green-800">
                                        <span class="w-1.5 h-1.5 mr-1.5 bg-green-500 rounded-full"></span>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                        Draft
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-3">
                                    {{-- Edit --}}
                                    <a href="{{ route('dashboard.info-magang-faq.edit', $faq->id) }}" 
                                       class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                                        Edit
                                    </a>
                                    
                                    {{-- Hapus --}}
                                    <form action="{{ route('dashboard.info-magang-faq.destroy', $faq->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus FAQ ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-medium text-red-600 dark:text-red-500 hover:underline">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum ada FAQ</h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 max-w-sm">
                                        Tambahkan pertanyaan umum untuk membantu calon peserta magang.
                                    </p>
                                    <a href="{{ route('dashboard.info-magang-faq.create') }}" class="mt-4 text-blue-600 hover:underline text-sm font-medium">
                                        Tambah FAQ Pertama
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination --}}
            @if($faqs->hasPages())
                <div class="mt-10 mb-12 px-2 sm:px-0">
                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                        {{-- Hapus class 'flex justify-center' agar pagination bisa melebar (justify-between) --}}
                        {{ $faqs->withQueryString()->links('vendor.pagination.tailwind') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-dashboard-layout>
