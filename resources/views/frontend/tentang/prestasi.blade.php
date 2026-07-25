<x-layout>
    {{-- Menggunakan default value jika $title tidak dikirim dari controller --}}
    <x-slot:title>{{ $title ?? 'Prestasi dan Penghargaan' }}</x-slot:title>

    {{-- CSS Khusus untuk Efek 3D Responsif --}}
    @push('styles')
    <style>
        /* Default Mobile: Flat (Tanpa Rotasi) */
        .prestasi-card-3d-left, 
        .prestasi-card-3d-right {
            transform: none;
            transition: transform 0.5s ease, box-shadow 0.5s ease;
        }

        /* Desktop (md ke atas): Terapkan efek 3D */
        @media (min-width: 768px) {
            .prestasi-card-3d-left {
                transform: rotateY(-10deg) rotateX(5deg);
            }
            .prestasi-card-3d-right {
                transform: rotateY(10deg) rotateX(5deg);
            }
            
            /* Hover Effect: Kembali datar & Zoom sedikit */
            .group:hover .prestasi-card-3d-left,
            .group:hover .prestasi-card-3d-right {
                transform: rotateY(0deg) rotateX(0deg) scale(1.02);
                z-index: 10;
            }
        }
    </style>
    @endpush

    {{-- WRAPPER UTAMA --}}
    <div class="px-4 sm:px-6 lg:px-8 py-8 sm:py-12 lg:py-16 space-y-12 md:space-y-20">
        
        {{-- HEADER HALAMAN --}}
        <div class="text-center max-w-3xl mx-auto mb-8 sm:mb-12">
            {{-- PERBAIKAN DI SINI: Tambahkan fallback "?? 'Judul Default'" --}}
            <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 dark:text-white tracking-tight">
                {{ $title ?? 'Prestasi dan Penghargaan' }}
            </h1>
            <p class="mt-4 text-base sm:text-lg text-gray-500 dark:text-gray-400 leading-relaxed">
                Jejak langkah dan pencapaian TVRI Stasiun D.I. Yogyakarta dalam memberikan layanan penyiaran terbaik.
            </p>
        </div>

        {{-- DAFTAR PRESTASI --}}
        <div class="space-y-12 md:space-y-16">
            @forelse($prestasis as $index => $prestasi)
                
                {{-- Logika Layout Zig-Zag --}}
                @php
                    $isOdd = $loop->odd; // True jika item ke-1, 3, 5...
                    
                    // Warna Gradient Background (Ganjil: Biru, Genap: Ungu/Pink)
                    $bgGradient = $isOdd
                        ? 'bg-gradient-to-br from-blue-50 to-indigo-100 dark:from-gray-900 dark:to-gray-800'
                        : 'bg-gradient-to-br from-purple-50 to-pink-100 dark:from-gray-900 dark:to-gray-800';

                    // Kelas Order (Desktop Only)
                    // Desktop Ganjil: Gambar Kiri (Normal)
                    // Desktop Genap:  Gambar Kanan (Swap order menggunakan flex/grid order)
                    $orderImage = $isOdd ? '' : 'md:order-last';
                    $orderText  = $isOdd ? '' : 'md:order-first';

                    // Kelas Efek 3D (Arah miring)
                    $class3D = $isOdd ? 'prestasi-card-3d-left' : 'prestasi-card-3d-right';
                @endphp

                <div class="group bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-0">
                        
                        {{-- 1. BAGIAN GAMBAR --}}
                        {{-- STRATEGI: Selalu taruh kode gambar DULUAN agar di Mobile (grid-cols-1) dia di atas --}}
                        <div class="md:col-span-2 relative aspect-[4/3] md:aspect-auto md:min-h-[400px] overflow-visible p-6 sm:p-8 flex items-center justify-center {{ $bgGradient }} {{ $orderImage }}">
                            
                            {{-- Wrapper 3D --}}
                            <div class="relative w-full max-w-sm md:max-w-full" style="perspective: 1000px;">
                                <div class="relative bg-white dark:bg-gray-700 rounded-xl shadow-2xl overflow-hidden {{ $class3D }}">
                                    @if($prestasi->image)
                                        <img class="w-full h-full object-cover transition-transform duration-700" 
                                             src="{{ Storage::url($prestasi->image) }}" 
                                             alt="{{ $prestasi->title }}">
                                    @else
                                        {{-- Placeholder Icon --}}
                                        <div class="aspect-[4/3] flex items-center justify-center bg-gray-100 dark:bg-gray-800">
                                            <svg class="w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                                            </svg>
                                        </div>
                                    @endif
                                    
                                    {{-- Inner Overlay --}}
                                    <div class="absolute inset-0 ring-1 ring-inset ring-black/5 dark:ring-white/10 rounded-xl pointer-events-none"></div>
                                </div>
                            </div>
                        </div>

                        {{-- 2. BAGIAN TEKS --}}
                        {{-- Di Desktop Genap, ini akan pindah ke kiri karena `md:order-first` --}}
                        <div class="md:col-span-3 p-6 sm:p-8 lg:p-12 flex flex-col justify-center bg-white dark:bg-gray-800 {{ $orderText }}">
                            
                            {{-- Badges: Tahun & Kategori --}}
                            <div class="flex flex-wrap items-center gap-2 mb-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300">
                                    {{ $prestasi->year }}
                                </span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 border border-gray-200 dark:border-gray-600">
                                    {{ $prestasi->category }}
                                </span>
                            </div>

                            {{-- Judul --}}
                            <h2 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mb-3 leading-tight">
                                {{ $prestasi->title }}
                            </h2>

                            {{-- Nama Penghargaan (Highlight) --}}
                            <div class="flex items-start gap-2 text-yellow-600 dark:text-yellow-400 font-semibold text-sm sm:text-base mb-6">
                                <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <span>{{ $prestasi->award_name }}</span>
                            </div>

                            {{-- Deskripsi --}}
                            <div class="prose prose-base sm:prose-lg dark:prose-invert text-gray-600 dark:text-gray-400 max-w-none">
                                {{-- Jika ada deskripsi utama, tampilkan --}}
                                @if($prestasi->description)
                                    <div class="text-gray-600 dark:text-gray-400">
                                        {{-- Render HTML Aman --}}
                                        {!! clean($prestasi->description) !!}
                                    </div>
                                @else
                                    <p>
                                        Penghargaan tingkat <strong>{{ $prestasi->type }}</strong> yang diraih atas dedikasi dan karya terbaik dalam industri penyiaran.
                                    </p>
                                @endif
                            </div>

                        </div>
                    </div>
                </div>

            @empty
                {{-- EMPTY STATE --}}
                <div class="text-center py-16 bg-gray-50 dark:bg-gray-800/50 rounded-3xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 dark:bg-gray-700 mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <circle cx="12" cy="8" r="7"></circle>
                            <polyline points="8.21 13.89 7 23 12 20 17 23 15.79 13.88"></polyline>
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Belum ada prestasi</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Data prestasi belum ditambahkan.</p>
                </div>
            @endforelse
        </div>

        {{-- PAGINASI --}}
        @if($prestasis->hasPages())
            <div class="mt-10 mb-12 px-2 sm:px-0">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                    {{-- Hapus class 'flex justify-center' agar pagination bisa melebar (justify-between) --}}
                    {{ $prestasis->withQueryString()->links('vendor.pagination.tailwind') }}
                </div>
            </div>
        @endif

    </div>
</x-layout>