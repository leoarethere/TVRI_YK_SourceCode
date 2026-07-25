<x-dashboard-layout>
    <x-slot:title>Akses Informasi — Pengaturan Link</x-slot:title>

    {{-- Header --}}
    <div class="mb-6 pt-2">
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Manajemen Akses Informasi</h1>
        <p class="mt-1 text-sm sm:text-base text-gray-500 dark:text-gray-400">
            Kelola link untuk tombol <strong>Akses Informasi Internal</strong> dan <strong>Akses Informasi Publik</strong> yang tampil di halaman beranda.
        </p>
    </div>

    {{-- Flash Message --}}
    @if(session('success'))
        <div class="mb-6 flex items-center gap-3 p-4 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-800 dark:text-green-300">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span class="text-sm font-medium">{{ session('success') }}</span>
        </div>
    @endif

    {{-- Preview Card (tampilan di beranda) --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm mb-6 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/60 flex items-center gap-2">
            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Preview Tampilan di Beranda</h3>
        </div>
        <div class="p-5">
            <div class="relative rounded-2xl overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 p-8 text-center">
                <div class="absolute inset-0 bg-grid-white/[0.05] bg-[size:24px_24px]"></div>
                <div class="relative z-10">
                    <p class="text-blue-100 text-xs mb-4 opacity-80">Tombol yang tampil di section "Layanan Satu Data TVRI"</p>
                    <div class="flex flex-wrap justify-center gap-3">
                        @if($internal->is_active)
                            <a href="{{ $internal->url ?: '#' }}" target="_blank"
                               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors shadow-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                                {{ $internal->label ?: 'Akses Informasi Internal' }}
                            </a>
                        @endif
                        @if($publik->is_active)
                            <a href="{{ $publik->url ?: '#' }}" target="_blank"
                               class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-xl transition-colors shadow-lg">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                                </svg>
                                {{ $publik->label ?: 'Akses Informasi Publik' }}
                            </a>
                        @endif
                        @if(!$internal->is_active && !$publik->is_active)
                            <p class="text-blue-200 text-sm italic">Semua tombol dinonaktifkan.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Form Cards (2 kolom) --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Card: Internal --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            {{-- Card Header --}}
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Akses Informasi Internal</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Tombol akses internal — khusus pegawai</p>
                </div>
                {{-- Status Badge --}}
                <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                    {{ $internal->is_active
                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                        : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $internal->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                    {{ $internal->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>

            <form action="{{ route('dashboard.akses-informasi.update', $internal) }}" method="POST" class="p-5 space-y-4">
                @csrf
                @method('PUT')

                {{-- URL --}}
                <div>
                    <label for="internal-url" class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">
                        URL Tujuan
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                        </div>
                        <input type="url" id="internal-url" name="url"
                               value="{{ old('url', $internal->url) }}"
                               placeholder="https://contoh.com/login-internal"
                               class="w-full pl-10 pr-3.5 py-2.5 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>
                    @error('url') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Label Override --}}
                <div>
                    <label for="internal-label" class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Label Tombol <span class="text-gray-400 font-normal text-xs">(opsional)</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <input type="text" id="internal-label" name="label"
                               value="{{ old('label', $internal->label) }}"
                               placeholder="Akses Informasi Internal"
                               class="w-full pl-10 pr-3.5 py-2.5 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Kosongkan untuk menggunakan teks default.</p>
                </div>

                {{-- Toggle Aktif --}}
                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600">
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Status Tombol</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Tampilkan tombol ini di beranda</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                               {{ $internal->is_active ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-sm focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </form>
        </div>

        {{-- Card: Publik --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
            {{-- Card Header --}}
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-gray-900 dark:text-white">Akses Informasi Publik</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Tombol akses publik — untuk masyarakat umum</p>
                </div>
                {{-- Status Badge --}}
                <span class="ml-auto inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                    {{ $publik->is_active
                        ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                        : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400' }}">
                    <span class="w-1.5 h-1.5 rounded-full mr-1.5 {{ $publik->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                    {{ $publik->is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
            </div>

            <form action="{{ route('dashboard.akses-informasi.update', $publik) }}" method="POST" class="p-5 space-y-4">
                @csrf
                @method('PUT')

                {{-- URL --}}
                <div>
                    <label for="publik-url" class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">
                        URL Tujuan
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                            </svg>
                        </div>
                        <input type="url" id="publik-url" name="url"
                               value="{{ old('url', $publik->url) }}"
                               placeholder="https://contoh.com/informasi-publik"
                               class="w-full pl-10 pr-3.5 py-2.5 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>
                    @error('url') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Label Override --}}
                <div>
                    <label for="publik-label" class="block mb-1.5 text-sm font-medium text-gray-700 dark:text-gray-300">
                        Label Tombol <span class="text-gray-400 font-normal text-xs">(opsional)</span>
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                        </div>
                        <input type="text" id="publik-label" name="label"
                               value="{{ old('label', $publik->label) }}"
                               placeholder="Akses Informasi Publik"
                               class="w-full pl-10 pr-3.5 py-2.5 text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Kosongkan untuk menggunakan teks default.</p>
                </div>

                {{-- Toggle Aktif --}}
                <div class="flex items-center justify-between p-3 rounded-lg bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600">
                    <div>
                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Status Tombol</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Tampilkan tombol ini di beranda</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="is_active" value="1" class="sr-only peer"
                               {{ $publik->is_active ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-blue-600"></div>
                    </label>
                </div>

                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-sm focus:ring-4 focus:ring-blue-300 dark:focus:ring-blue-800">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </form>
        </div>

    </div>
</x-dashboard-layout>
