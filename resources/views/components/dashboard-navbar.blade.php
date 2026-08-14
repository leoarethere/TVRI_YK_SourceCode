{{-- resources/views/components/dashboard-navbar.blade.php --}}
<nav class="bg-white dark:bg-gray-800 fixed w-full top-0 z-50 border-b border-gray-200 dark:border-gray-700 transition-colors duration-300 shadow-sm"
     x-data="{
         isMobileMenuOpen: false,
         openDropdown: null,
         timeoutId: null,
         resizeHandler: null,
         wasDesktop: window.innerWidth >= 1280,
         init() {
             this.resizeHandler = () => {
                 const isDesktop = window.innerWidth >= 1280;
                 if (isDesktop !== this.wasDesktop) {
                     this.closeMenus();
                     this.wasDesktop = isDesktop;
                 }
             };
             window.addEventListener('resize', this.resizeHandler, { passive: true });
         },
         openMenu(name) {
             clearTimeout(this.timeoutId);
             this.openDropdown = name;
         },
         closeMenu() {
             this.timeoutId = setTimeout(() => {
                 this.openDropdown = null;
             }, 300);
         },
         toggleMenu(name) {
             clearTimeout(this.timeoutId);
             this.openDropdown = this.openDropdown === name ? null : name;
         },
         closeMenus() {
             clearTimeout(this.timeoutId);
             this.openDropdown = null;
             this.isMobileMenuOpen = false;
         },
         destroy() {
             this.closeMenus();
             window.removeEventListener('resize', this.resizeHandler);
         }
     }"
     @keydown.escape.window="closeMenus()"
     @click.outside="closeMenus()">
    
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="relative flex h-16 sm:h-[72px] items-center justify-between">
            
            {{-- BAGIAN KIRI: Toggle Sidebar & Logo --}}
            <div class="flex items-center gap-1">
                {{-- Toggle Sidebar: mobile dan tombol pemulihan saat desktop diciutkan --}}
                <button 
                    @click="isSidebarOpen = !isSidebarOpen"
                    x-show="isMobile() || !isSidebarOpen"
                    x-cloak
                    data-dashboard-critical
                    data-dashboard-sidebar-toggle
                    type="button"
                    class="-ml-2 rounded-lg p-2 text-gray-600 transition-colors hover:bg-gray-100 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white dark:focus:ring-gray-700"
                    :aria-expanded="isSidebarOpen"
                    :aria-label="isSidebarOpen ? 'Tutup navigasi dashboard' : 'Buka navigasi dashboard'"
                    aria-controls="dashboard-sidebar">
                    <span class="sr-only">Buka navigasi dashboard</span>
                    {{-- Heroicon: Bars 3 --}}
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>

                {{-- Logo Link --}}
                <a href="{{ url('/') }}" class="flex items-center gap-1">
                    {{-- Logo Image: Ukuran responsif --}}
                    <img src="{{ asset('img/logolight.png') }}" 
                        class="h-8 sm:h-9 md:h-10 w-auto block dark:hidden" 
                        alt="Logo Light" />
                    
                    <img src="{{ asset('img/logodark.png') }}" 
                        class="h-8 sm:h-9 md:h-10 w-auto hidden dark:block" 
                        alt="Logo Dark" />

                    {{-- Logo Text: Ukuran responsif --}}
                    <span class="hidden text-gray-900 dark:text-white font-bold sm:inline sm:text-xl md:text-2xl whitespace-nowrap">
                        Yogyakarta
                    </span>
                </a>
            </div>

            {{-- BAGIAN KANAN (DESKTOP): Menu & User Dropdown --}}
            <div class="hidden items-center gap-4 xl:flex">
                {{-- Theme Toggle --}}
                <div class="flex items-center">
                    <x-theme-toggle />
                </div>

                {{-- User Dropdown --}}
                <div class="relative" @mouseenter="openMenu('user')" @mouseleave="closeMenu()">
                    <button 
                        type="button"
                        @click="toggleMenu('user')"
                        :aria-expanded="openDropdown === 'user'"
                        aria-controls="dashboard-user-menu"
                        class="flex items-center gap-3 rounded-full bg-gray-50 dark:bg-gray-700/50 hover:bg-gray-100 dark:hover:bg-gray-700 border border-gray-200 dark:border-gray-600 pl-1 pr-4 py-1 transition-all duration-200 group">
                        
                        <img class="w-8 h-8 rounded-full ring-2 ring-white dark:ring-gray-600" 
                             src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random&color=fff" 
                             alt="{{ auth()->user()->name }}" />
                        
                        <div class="text-left hidden xl:block">
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-200 group-hover:text-gray-900 dark:group-hover:text-white leading-none">
                                {{ Str::limit(auth()->user()->name, 15) }}
                            </p>
                        </div>

                        <svg class="w-4 h-4 text-gray-400 group-hover:text-gray-600 dark:text-gray-400 dark:group-hover:text-white transition-transform duration-200" 
                             :class="{ 'rotate-180': openDropdown === 'user' }" 
                             fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                        </svg>
                    </button>
                    
                    {{-- Dropdown Content --}}
                    <div id="dashboard-user-menu" x-show="openDropdown === 'user'"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 translate-y-2"
                         class="absolute right-0 mt-2 w-56 bg-white dark:bg-gray-800 rounded-xl shadow-lg ring-1 ring-black ring-opacity-5 z-20 py-2 border border-gray-100 dark:border-gray-700" 
                         style="display: none;">
                        
                        <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 mb-1">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
                        </div>

                        <div class="px-1">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" 
                                    class="w-full flex items-center gap-3 px-3 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                    {{-- Heroicon: Arrow Right On Rectangle --}}
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H9" />
                                    </svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BAGIAN KANAN (MOBILE): Theme & User Toggle --}}
            <div class="flex items-center gap-2 xl:hidden">
                {{-- Theme Toggle Mobile --}}
                <x-theme-toggle />

                {{-- Mobile Menu Button --}}
                <button @click="isMobileMenuOpen = !isMobileMenuOpen" 
                        type="button" 
                        :aria-expanded="isMobileMenuOpen"
                        aria-controls="dashboard-mobile-user-menu"
                        class="inline-flex items-center justify-center p-2 rounded-lg text-gray-500 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-blue-500 transition-colors">
                    <span class="sr-only">Buka menu akun</span>
                    
                    {{-- Avatar saat menu tertutup --}}
                    <img class="h-8 w-8 rounded-full ring-2 ring-gray-100 dark:ring-gray-700" 
                         src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random&color=fff" 
                         alt="{{ auth()->user()->name }}" 
                         :class="{ 'hidden': isMobileMenuOpen, 'block': !isMobileMenuOpen }"/>
                    
                    {{-- Icon Close saat menu terbuka --}}
                    {{-- Heroicon: X Mark --}}
                    <svg class="h-6 w-6" :class="{ 'block': isMobileMenuOpen, 'hidden': !isMobileMenuOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    {{-- MOBILE MENU DROPDOWN --}}
    <div id="dashboard-mobile-user-menu" x-show="isMobileMenuOpen"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 translate-y-2"
         class="border-t border-gray-200 bg-white/95 shadow-xl backdrop-blur-sm dark:border-gray-700 dark:bg-gray-800/95 xl:hidden"
         style="display: none;">
        
        <div class="px-4 pt-4 pb-6 space-y-4">
            
            {{-- User Info Mobile --}}
            <div class="flex items-center gap-4 px-2">
                <img class="w-12 h-12 rounded-full ring-2 ring-blue-500/30" 
                     src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=random&color=fff" 
                     alt="{{ auth()->user()->name }}" />
                <div class="flex-1 min-w-0">
                    <p class="text-base font-bold text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>

            <div class="border-t border-gray-100 dark:border-gray-700 pt-2">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" 
                        class="w-full flex items-center gap-3 px-4 py-3 text-base font-medium text-red-600 bg-red-50 hover:bg-red-100 dark:bg-red-900/10 dark:text-red-400 dark:hover:bg-red-900/20 rounded-xl transition-all duration-200">
                        {{-- Heroicon: Arrow Right On Rectangle --}}
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l3 3m0 0l-3 3m3-3H9" />
                        </svg>
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
