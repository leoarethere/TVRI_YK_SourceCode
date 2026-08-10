<x-dashboard-layout>
    <x-slot:title>Kelola Media Sosial</x-slot:title>

    <div class="pb-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-8 pt-2">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                    Kelola Media Sosial
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Daftar tautan media sosial instansi.
                </p>
            </div>

            {{-- Tombol Edit Biru --}}
            @if($socialMedia)
                <a href="{{ route('dashboard.social-media.edit', $socialMedia->id) }}" 
                   class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit Media Sosial
                </a>
            @endif
        </div>

        {{-- @if(session('success'))
            <div class="mb-6 p-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800" role="alert">
                <span class="font-medium">Sukses!</span> {{ session('success') }}
            </div>
        @endif --}}

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {{-- Helper function untuk menampilkan link --}}
            @php
                function showLink($url) {
                    if (!$url) {
                        return '<div class="flex items-center text-gray-400 dark:text-gray-500 italic text-sm py-1"><svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg> Belum ada tautan</div>';
                    }
                    $escaped = e($url);
                    return '<a href="'.$escaped.'" target="_blank" rel="noopener noreferrer" class="flex items-center text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors text-sm font-medium py-1 group" title="'.$escaped.'"><svg class="w-4 h-4 mr-2 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg><span class="truncate">'.$escaped.'</span></a>';
                }
            @endphp

            {{-- Instagram Card --}}
            <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300 overflow-hidden">
                <div class="flex items-center gap-4 mb-4 relative z-10">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Instagram</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">@if($socialMedia && $socialMedia->instagram) Tersambung @else Belum Diatur @endif</p>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900/50 p-3 rounded-xl border border-gray-100 dark:border-gray-700 relative z-10">
                    {!! showLink($socialMedia->instagram ?? null) !!}
                </div>
            </div>

            {{-- Facebook Card --}}
            <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300 overflow-hidden">
                <div class="flex items-center gap-4 mb-4 relative z-10">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Facebook</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">@if($socialMedia && $socialMedia->facebook) Tersambung @else Belum Diatur @endif</p>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900/50 p-3 rounded-xl border border-gray-100 dark:border-gray-700 relative z-10">
                    {!! showLink($socialMedia->facebook ?? null) !!}
                </div>
            </div>

            {{-- Twitter / X Card --}}
            <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300 overflow-hidden">
                <div class="flex items-center gap-4 mb-4 relative z-10">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">X (Twitter)</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">@if($socialMedia && $socialMedia->twitter) Tersambung @else Belum Diatur @endif</p>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900/50 p-3 rounded-xl border border-gray-100 dark:border-gray-700 relative z-10">
                    {!! showLink($socialMedia->twitter ?? null) !!}
                </div>
            </div>

            {{-- YouTube Card --}}
            <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300 overflow-hidden">
                <div class="flex items-center gap-4 mb-4 relative z-10">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">YouTube</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">@if($socialMedia && $socialMedia->youtube) Channel Terhubung @else Belum Diatur @endif</p>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900/50 p-3 rounded-xl border border-gray-100 dark:border-gray-700 relative z-10">
                    {!! showLink($socialMedia->youtube ?? null) !!}
                </div>
            </div>

            {{-- TikTok Card --}}
            <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 p-6 hover:shadow-md transition-all duration-300 overflow-hidden md:col-span-2 lg:col-span-1">
                <div class="flex items-center gap-4 mb-4 relative z-10">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">TikTok</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 font-medium">@if($socialMedia && $socialMedia->tiktok) Tersambung @else Belum Diatur @endif</p>
                    </div>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900/50 p-3 rounded-xl border border-gray-100 dark:border-gray-700 relative z-10">
                    {!! showLink($socialMedia->tiktok ?? null) !!}
                </div>
            </div>

        </div>
    </div>
</x-dashboard-layout>
