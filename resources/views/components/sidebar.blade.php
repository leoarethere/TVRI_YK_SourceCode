<aside 
    class="fixed top-0 left-0 z-40 h-screen bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-all duration-300 ease-in-out md:translate-x-0 will-change-transform"
    x-data="{ 
        isHovering: false,
        dropdowns: {
            publikasi: false,
            layanan: false,
            tentang: false
        },
        
        initDropdowns() {
            // Set dropdown terbuka jika route aktif saat sidebar terbuka
            if (this.isSidebarOpen) {
                this.dropdowns.publikasi = {{ request()->is('dashboard/penyiaran*', 'dashboard/jadwal-acara*') ? 'true' : 'false' }};
                this.dropdowns.layanan = {{ request()->is('dashboard/ppid*', 'dashboard/info-rb*', 'dashboard/info-magang*', 'dashboard/info-magang-faq*', 'dashboard/info-kunjungan*', 'dashboard/info-kunjungan-faq*') ? 'true' : 'false' }};
                this.dropdowns.tentang = {{ request()->is('dashboard/sejarah*', 'dashboard/visi-misi*', 'dashboard/prestasi*', 'dashboard/tugas-fungsi*', 'dashboard/himne-tvri*') ? 'true' : 'false' }};
            }
        },
        
        closeAllDropdowns() {
            this.dropdowns.publikasi = false;
            this.dropdowns.layanan = false;
            this.dropdowns.tentang = false;
        },
        
        toggleDropdown(dropdownName) {
            if (!this.isSidebarOpen) {
                // Jika sidebar tertutup, buka sidebar dulu
                this.isSidebarOpen = true;
                this.closeAllDropdowns();
                // Buka dropdown dengan jeda agar animasi mulus
                setTimeout(() => {
                    this.dropdowns[dropdownName] = true;
                }, 150);
            } else {
                // Tutup semua dropdown lain kecuali yang diklik
                Object.keys(this.dropdowns).forEach(key => {
                    if (key !== dropdownName) {
                        this.dropdowns[key] = false;
                    }
                });
                // Toggle dropdown yang diklik
                this.dropdowns[dropdownName] = !this.dropdowns[dropdownName];
            }
        }
    }"
    x-init="
        initDropdowns();
        
        // Reset dropdowns saat sidebar berubah state
        $watch('isSidebarOpen', value => {
            if (!value) {
                setTimeout(() => closeAllDropdowns(), 150);
            } else {
                // Saat sidebar dibuka, set ulang berdasarkan route aktif
                setTimeout(() => initDropdowns(), 300);
            }
        });
    "
    :class="isSidebarOpen 
        ? 'translate-x-0 w-64' 
        : '-translate-x-full md:translate-x-0 md:w-20'"
    style="padding-top: 4.5rem;">

    {{-- Sidebar Content --}}
    {{-- PERBAIKAN: overflow-hidden dihapus dari sini dan dipindah ke logic kondisional di bawah --}}
    {{-- Agar saat collapse, submenu yang absolute tidak terpotong --}}
    <div class="flex flex-col h-full" 
         :class="isSidebarOpen ? 'overflow-hidden' : 'overflow-visible'">

        {{-- Menu Container --}}
        <div class="flex-1 py-5 px-3 scrollbar-hide transition-all duration-300"
             :class="isSidebarOpen ? 'overflow-y-auto overflow-x-hidden' : 'overflow-visible'">

            {{-- Menu Items --}}
            <ul class="space-y-2">

                {{-- Dashboard --}}
                <x-sidebar-item href="{{ url('/dashboard') }}" :active="request()->routeIs('dashboard')">
                    <x-slot:icon>
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                    </x-slot:icon>
                    Dashboard
                </x-sidebar-item>

                {{-- Dropdown Publikasi --}}
                @php 
                    $isPublikasiActive = request()->is('dashboard/penyiaran*', 'dashboard/jadwal-acara*');
                    $isBroadcastsActive = request()->is('dashboard/penyiaran*');
                    $isJadwalActive = request()->is('dashboard/jadwal-acara*');
                @endphp
                
                <li class="relative">
                    
                    <button 
                        @click.prevent="toggleDropdown('publikasi')"
                        class="flex items-center w-full p-2 text-base font-medium rounded-lg transition-all duration-300 group relative"
                        :class="{ 
                            'justify-center': !isSidebarOpen,
                            'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white': {{ $isPublikasiActive ? 'true' : 'false' }} || dropdowns.publikasi,
                            'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700': !{{ $isPublikasiActive ? 'true' : 'false' }}
                        }">
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" 
                             :class="{
                                'text-gray-900 dark:text-white': {{ $isPublikasiActive ? 'true' : 'false' }} || dropdowns.publikasi,
                                'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white': !{{ $isPublikasiActive ? 'true' : 'false' }}
                             }"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                        
                        {{-- PERBAIKAN: Animasi Text disamakan duration-300 --}}
                        <span class="ml-3 flex-1 text-left whitespace-nowrap overflow-hidden" 
                              x-show="isSidebarOpen"
                              x-transition:enter="transition ease-out duration-300"
                              x-transition:enter-start="opacity-0 -translate-x-2"
                              x-transition:enter-end="opacity-100 translate-x-0"
                              x-transition:leave="transition ease-in duration-800"
                              x-transition:leave-start="opacity-100 translate-x-0"
                              x-transition:leave-end="opacity-0 -translate-x-2">
                            Publikasi
                        </span>
                        
                        {{-- Icon Chevron --}}
                        <svg class="w-4 h-4 ml-auto transition-transform duration-300 flex-shrink-0" 
                             :class="{ 'rotate-90': dropdowns.publikasi }" 
                             x-show="isSidebarOpen"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100" 
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </button>

                    {{-- Submenu Publikasi --}}
                    <ul x-show="dropdowns.publikasi" 
                        x-transition:enter="transition-all ease-out duration-300"
                        x-transition:enter-start="opacity-0 max-h-0"
                        x-transition:enter-end="opacity-100 max-h-96"
                        x-transition:leave="transition-all ease-in duration-200"
                        x-transition:leave-start="opacity-100 max-h-96"
                        x-transition:leave-end="opacity-0 max-h-0"
                        class="pl-11 mt-1 space-y-1 overflow-hidden"
                        x-cloak>
                        
                        <li>
                            <a href="{{ route('dashboard.penyiaran.index') }}" 
                            class="flex items-center gap-3 py-2 px-3 text-sm rounded-md transition-colors duration-200 group"
                            :class="{
                                'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white font-medium': {{ $isBroadcastsActive ? 'true' : 'false' }},
                                'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white': !{{ $isBroadcastsActive ? 'true' : 'false' }}
                            }">
                                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Penyiaran
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/dashboard/jadwal-acara') }}" 
                               class="flex items-center gap-3 py-2 px-3 text-sm rounded-md transition-colors duration-200 group"
                               :class="{
                                   'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white font-medium': {{ $isJadwalActive ? 'true' : 'false' }},
                                   'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white': !{{ $isJadwalActive ? 'true' : 'false' }}
                               }">
                                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                Jadwal Acara
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Dropdown Layanan --}}
                @php 
                    // [UPDATE] Tambahkan 'dashboard/info-kunjungan-faq*' agar sidebar tetap terbuka
                    $isLayananActive = request()->is('dashboard/ppid*', 'dashboard/info-rb*', 'dashboard/info-magang*', 'dashboard/info-magang-faq*', 'dashboard/info-kunjungan*', 'dashboard/info-kunjungan-faq*');
                @endphp
                <li class="relative">
                    
                    <button 
                        @click.prevent="toggleDropdown('layanan')"
                        class="flex items-center w-full p-2 text-base font-medium rounded-lg transition-all duration-300 group relative"
                        :class="{ 
                            'justify-center': !isSidebarOpen,
                            'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white': {{ $isLayananActive ? 'true' : 'false' }} || dropdowns.layanan,
                            'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700': !{{ $isLayananActive ? 'true' : 'false' }}
                        }">
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" 
                             :class="{
                                'text-gray-900 dark:text-white': {{ $isLayananActive ? 'true' : 'false' }} || dropdowns.layanan,
                                'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white': !{{ $isLayananActive ? 'true' : 'false' }}
                             }"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11" />
                        </svg>
                        <span class="ml-3 flex-1 text-left whitespace-nowrap overflow-hidden" 
                              x-show="isSidebarOpen"
                              x-transition:enter="transition ease-out duration-300"
                              x-transition:enter-start="opacity-0 -translate-x-2"
                              x-transition:enter-end="opacity-100 translate-x-0"
                              x-transition:leave="transition ease-in duration-800"
                              x-transition:leave-start="opacity-100 translate-x-0"
                              x-transition:leave-end="opacity-0 -translate-x-2">
                            Layanan
                        </span>
                        <svg class="w-4 h-4 ml-auto transition-transform duration-300 flex-shrink-0" 
                             :class="{ 'rotate-90': dropdowns.layanan }" 
                             x-show="isSidebarOpen"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100" 
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="9 18 15 12 9 6"></polyline>
                        </svg>
                    </button>

                    <ul x-show="dropdowns.layanan" 
                        x-transition:enter="transition-all ease-out duration-300"
                        x-transition:enter-start="opacity-0 max-h-0"
                        x-transition:enter-end="opacity-100 max-h-[500px]"
                        x-transition:leave="transition-all ease-in duration-200"
                        x-transition:leave-start="opacity-100 max-h-[500px]"
                        x-transition:leave-end="opacity-0 max-h-0"
                        class="pl-11 mt-1 space-y-1 overflow-hidden"
                        x-cloak>
                        
                        <li>
                            <a href="{{ url('/dashboard/ppid') }}" 
                               class="flex items-center gap-3 py-2 px-3 text-sm rounded-md transition-colors duration-200 group"
                               :class="{
                                   'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white font-medium': {{ request()->is('dashboard/ppid*') ? 'true' : 'false' }},
                                   'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white': !{{ request()->is('dashboard/ppid*') ? 'true' : 'false' }}
                               }">
                                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                </svg>
                                Informasi PPID
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/dashboard/info-rb') }}" 
                               class="flex items-center gap-3 py-2 px-3 text-sm rounded-md transition-colors duration-200 group"
                               :class="{
                                   'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white font-medium': {{ request()->is('dashboard/info-rb*') ? 'true' : 'false' }},
                                   'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white': !{{ request()->is('dashboard/info-rb*') ? 'true' : 'false' }}
                               }">
                                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                Informasi RB
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/dashboard/info-magang') }}" 
                               class="flex items-center gap-3 py-2 px-3 text-sm rounded-md transition-colors duration-200 group"
                               :class="{
                                   'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white font-medium': {{ request()->is('dashboard/info-magang*') ? 'true' : 'false' }},
                                   'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white': !{{ request()->is('dashboard/info-magang*') ? 'true' : 'false' }}
                               }">
                                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Informasi Magang / PKL
                            </a>
                        </li>

                        {{-- [BARU] MENU FAQ MAGANG --}}
                        <li>
                            <a href="{{ route('dashboard.info-magang-faq.index') }}" 
                               class="flex items-center gap-3 py-2 px-3 text-sm rounded-md transition-colors duration-200 group"
                               :class="{
                                   'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white font-medium': {{ request()->is('dashboard/info-magang-faq*') ? 'true' : 'false' }},
                                   'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white': !{{ request()->is('dashboard/info-magang-faq*') ? 'true' : 'false' }}
                               }">
                                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                FAQ Magang
                            </a>
                        </li>

                        <li>
                            <a href="{{ url('/dashboard/info-kunjungan') }}" 
                               class="flex items-center gap-3 py-2 px-3 text-sm rounded-md transition-colors duration-200 group"
                               :class="{
                                   'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white font-medium': {{ request()->is('dashboard/info-kunjungan*') ? 'true' : 'false' }},
                                   'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white': !{{ request()->is('dashboard/info-kunjungan*') ? 'true' : 'false' }}
                               }">
                                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                    <path d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Informasi Kunjungan
                            </a>
                        </li>

                        {{-- [BARU] Menu FAQ Kunjungan --}}
                        <li>
                            <a href="{{ route('dashboard.info-kunjungan-faq.index') }}" 
                               class="flex items-center gap-3 py-2 px-3 text-sm rounded-md transition-colors duration-200 group"
                               :class="{
                                   'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white font-medium': {{ request()->is('dashboard/info-kunjungan-faq*') ? 'true' : 'false' }},
                                   'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white': !{{ request()->is('dashboard/info-kunjungan-faq*') ? 'true' : 'false' }}
                               }">
                                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                FAQ Kunjungan
                            </a>
                        </li>
                        
                    </ul>
                </li>

                {{-- Berita --}}
                <x-sidebar-item href="{{ route('dashboard.berita.index') }}" :active="request()->routeIs('dashboard.berita*')">
                    <x-slot:icon>
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                    </x-slot:icon>
                    Berita
                </x-sidebar-item>

                {{-- Postingan --}}
                <x-sidebar-item href="{{ route('dashboard.postingan.index') }}" :active="request()->is('dashboard/postingan*')">
                    <x-slot:icon>
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z" />
                        </svg>
                    </x-slot:icon>
                    Postingan
                </x-sidebar-item>

                {{-- Dropdown Tentang --}}
                @php 
                    $isTentangActive = request()->is('dashboard/sejarah*', 'dashboard/visi-misi*', 'dashboard/prestasi*', 'dashboard/tugas-fungsi*', 'dashboard/himne-tvri*');
                @endphp
                <li class="relative">
                    
                    <button 
                        @click.prevent="toggleDropdown('tentang')"
                        class="flex items-center w-full p-2 text-base font-medium rounded-lg transition-all duration-300 group relative"
                        :class="{ 
                            'justify-center': !isSidebarOpen,
                            'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white': {{ $isTentangActive ? 'true' : 'false' }} || dropdowns.tentang,
                            'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700': !{{ $isTentangActive ? 'true' : 'false' }}
                        }">
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" 
                             :class="{
                                'text-gray-900 dark:text-white': {{ $isTentangActive ? 'true' : 'false' }} || dropdowns.tentang,
                                'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white': !{{ $isTentangActive ? 'true' : 'false' }}
                             }"
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="ml-3 flex-1 text-left whitespace-nowrap overflow-hidden" 
                              x-show="isSidebarOpen"
                              x-transition:enter="transition ease-out duration-300"
                              x-transition:enter-start="opacity-0 -translate-x-2"
                              x-transition:enter-end="opacity-100 translate-x-0"
                              x-transition:leave="transition ease-in duration-800"
                              x-transition:leave-start="opacity-100 translate-x-0"
                              x-transition:leave-end="opacity-0 -translate-x-2">
                            Tentang
                        </span>
                        <svg class="w-4 h-4 ml-auto transition-transform duration-300 flex-shrink-0" 
                             :class="{ 'rotate-90': dropdowns.tentang }" 
                             x-show="isSidebarOpen"
                             x-transition:enter="transition ease-out duration-300"
                             x-transition:enter-start="opacity-0"
                             x-transition:enter-end="opacity-100" 
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>

                    <ul x-show="dropdowns.tentang" 
                        x-transition:enter="transition-all ease-out duration-300"
                        x-transition:enter-start="opacity-0 max-h-0"
                        x-transition:enter-end="opacity-100 max-h-[500px]"
                        x-transition:leave="transition-all ease-in duration-200"
                        x-transition:leave-start="opacity-100 max-h-[500px]"
                        x-transition:leave-end="opacity-0 max-h-0"
                        class="pl-11 mt-1 space-y-1 overflow-hidden"
                        x-cloak>
                        
                        <li>
                            <a href="{{ url('/dashboard/sejarah') }}" 
                               class="flex items-center gap-3 py-2 px-3 text-sm rounded-md transition-colors duration-200 group"
                               :class="{
                                   'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white font-medium': {{ request()->is('dashboard/sejarah*') ? 'true' : 'false' }},
                                   'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white': !{{ request()->is('dashboard/sejarah*') ? 'true' : 'false' }}
                               }">
                                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Sejarah
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/dashboard/visi-misi') }}" 
                               class="flex items-center gap-3 py-2 px-3 text-sm rounded-md transition-colors duration-200 group"
                               :class="{
                                   'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white font-medium': {{ request()->is('dashboard/visi-misi*') ? 'true' : 'false' }},
                                   'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white': !{{ request()->is('dashboard/visi-misi*') ? 'true' : 'false' }}
                               }">
                                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Visi dan Misi
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/dashboard/tugas-fungsi') }}" 
                               class="flex items-center gap-3 py-2 px-3 text-sm rounded-md transition-colors duration-200 group"
                               :class="{
                                   'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white font-medium': {{ request()->is('dashboard/tugas-fungsi*') ? 'true' : 'false' }},
                                   'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white': !{{ request()->is('dashboard/tugas-fungsi*') ? 'true' : 'false' }}
                               }">
                                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                Tugas dan Fungsi
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/dashboard/prestasi') }}" 
                               class="flex items-center gap-3 py-2 px-3 text-sm rounded-md transition-colors duration-200 group"
                               :class="{
                                   'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white font-medium': {{ request()->is('dashboard/prestasi*') ? 'true' : 'false' }},
                                   'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white': !{{ request()->is('dashboard/prestasi*') ? 'true' : 'false' }}
                               }">
                                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M12 8.5v7m0-7V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7" />
                                </svg>
                                Prestasi
                            </a>
                        </li>
                        <li>
                            <a href="{{ url('/dashboard/himne-tvri') }}" 
                               class="flex items-center gap-3 py-2 px-3 text-sm rounded-md transition-colors duration-200 group"
                               :class="{
                                   'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white font-medium': {{ request()->is('dashboard/himne-tvri*') ? 'true' : 'false' }},
                                   'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 hover:text-gray-900 dark:hover:text-white': !{{ request()->is('dashboard/himne-tvri*') ? 'true' : 'false' }}
                               }">
                                <svg class="h-5 w-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" />
                                </svg>
                                Himne TVRI
                            </a>
                        </li>
                    </ul>
                </li>

            </ul>
            
            {{-- Divider --}}
            <hr class="my-6 border-t border-gray-300 dark:border-gray-600" />

            {{-- Menu Banner & Pengaturan Lainnya --}}
            <ul class="mt-2 space-y-2">
                <x-sidebar-item href="{{ url('/dashboard/banners') }}" :active="request()->is('dashboard/banners*')">
                    <x-slot:icon>
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" 
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </x-slot:icon>
                    Banner Carousel
                </x-sidebar-item>

                @if(auth()->user()->isAdmin())
                {{-- MANAJEMEN USER --}}
                <x-sidebar-item href="{{ route('dashboard.users.index') }}" :active="request()->is('dashboard/users*')">
                    <x-slot:icon>
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" 
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                            <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </x-slot:icon>
                    Manajemen Pengguna
                </x-sidebar-item>
                @endif

                {{-- MANAJEMEN KOMENTAR --}}
                <x-sidebar-item href="{{ route('dashboard.komentar.index') }}" :active="request()->is('dashboard/komentar*')">
                    <x-slot:icon>
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" 
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path>
                        </svg>
                    </x-slot:icon>
                    Manajemen Komentar
                </x-sidebar-item>

                @if(auth()->user()->isAdmin())
                {{-- SOSIAL MEDIA --}}
                <x-sidebar-item href="{{ route('dashboard.social-media.index') }}" :active="request()->is('dashboard/social-media*')">
                    <x-slot:icon>
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" 
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="18" cy="5" r="3"></circle>
                            <circle cx="6" cy="12" r="3"></circle>
                            <circle cx="18" cy="19" r="3"></circle>
                            <line x1="8.59" y1="13.51" x2="15.42" y2="17.49"></line>
                            <line x1="15.41" y1="6.51" x2="8.59" y2="10.49"></line>
                        </svg>
                    </x-slot:icon>
                    Sosial Media
                </x-sidebar-item>

                {{-- HUBUNGI KAMI --}}
                <x-sidebar-item href="{{ route('dashboard.contact-info.index') }}" :active="request()->routeIs('dashboard.contact-info*')">
                    <x-slot:icon>
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                    </x-slot:icon>
                    Hubungi Kami
                </x-sidebar-item>

                {{-- AKSES INFORMASI --}}
                <x-sidebar-item href="{{ route('dashboard.akses-informasi.index') }}" :active="request()->routeIs('dashboard.akses-informasi*')">
                    <x-slot:icon>
                        <svg class="w-6 h-6 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/>
                        </svg>
                    </x-slot:icon>
                    Akses Informasi
                </x-sidebar-item>
                @endif
            </ul>
        </div>

        {{-- Sidebar Footer --}}
        <div class="p-3 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <button 
                @click="isSidebarOpen = !isSidebarOpen; closeAllDropdowns()"
                class="hidden md:flex w-full items-center p-3 text-sm font-medium text-gray-500 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors duration-200 group"
                :class="{ 'justify-center': !isSidebarOpen }">
                
                <svg class="w-5 h-5 flex-shrink-0 transition-transform duration-300 group-hover:scale-110" 
                     :class="{ 'rotate-180': !isSidebarOpen }"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="11 17 6 12 11 7"></polyline><polyline points="18 17 13 12 18 7"></polyline>
                </svg>

                <span class="ml-3 overflow-hidden whitespace-nowrap"
                      x-show="isSidebarOpen"
                      x-transition:enter="transition ease-out duration-300"
                      x-transition:enter-start="opacity-0 -translate-x-2"
                      x-transition:enter-end="opacity-100 translate-x-0"
                      x-transition:leave="transition ease-in duration-800"
                      x-transition:leave-start="opacity-100 translate-x-0"
                      x-transition:leave-end="opacity-0 -translate-x-2">
                    Ciutkan
                </span>
            </button>
        </div>
    </div>
</aside>
