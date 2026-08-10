<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="turbo-visit-control" content="reload">
    {{-- Mencegah halaman admin/dashboard terindeks oleh mesin pencari --}}
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $title ?? 'Dashboard Admin' }}</title>
    
    @vite([
        'resources/css/app.css', 
        'resources/js/app.js', 
        'resources/js/dashboard.js' 
    ])
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans+Flex:opsz,wght@6..144,1..1000&display=swap" rel="stylesheet">

    <style>
        [x-cloak] { display: none !important; }
        .turbo-loading { opacity: 0.7; pointer-events: none; }
        .notification-banner { transition: all 0.5s cubic-bezier(0.68, -0.55, 0.27, 1.55); }
    </style>

    <x-favicon />
</head>
<body class="overflow-x-hidden bg-gray-50 dark:bg-gray-900">

    {{-- TAMBAHKAN @show-notification.window DI SINI --}}
    {{-- Ini menjamin listener aktif tepat saat komponen dibuat --}}
    <div x-data="dashboardState()" 
         x-init="init()"
         @show-notification.window="showNotification($event.detail.message, $event.detail.type)"
         @show-modal.window="showModal($event.detail.title, $event.detail.message, $event.detail.type)"
         class="antialiased">

        <div id="flash-container"></div>
        <x-status-modal />
        <x-notification-trigger />
        <x-dashboard-navbar />
        <x-sidebar />

        {{-- Backdrop untuk Mobile --}}
        <div x-show="isSidebarOpen && isMobile()" 
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="closeSidebar()"
            class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden"
            style="display: none;">
        </div>
        
{{-- UI NOTIFIKASI TOAST (Desain Blok Solid) --}}
        <div 
            x-show="notificationOpen"
            x-transition:enter="notification-banner transform ease-out duration-300 transition"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            x-transition:leave="notification-banner transition ease-in duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            x-cloak
            class="fixed top-20 left-4 right-4 z-50 w-auto rounded-lg border-0 p-4 shadow-xl sm:left-auto sm:w-full sm:max-w-sm"
            :class="{
                'bg-green-600 text-white dark:bg-green-600': notificationType === 'success',
                'bg-red-600 text-white dark:bg-red-600': notificationType === 'error'
            }"
            role="alert">
            <div class="flex items-start">
                {{-- Ikon --}}
                <div class="flex-shrink-0">
                    <svg x-show="notificationType === 'success'" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    <svg x-show="notificationType === 'error'" class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                {{-- Pesan --}}
                <div class="ml-3 text-sm font-medium text-white shadow-sm" x-text="notificationMessage"></div>
                
                {{-- Tombol Tutup --}}
                <button type="button" 
                    @click="notificationOpen = false" 
                    class="ml-auto -mx-1.5 -my-1.5 rounded-lg p-1.5 inline-flex h-8 w-8 transition-colors duration-200 focus:outline-none"
                    :class="{
                        'text-green-100 hover:bg-green-700 hover:text-white': notificationType === 'success',
                        'text-red-100 hover:bg-red-700 hover:text-white': notificationType === 'error'
                    }">
                    <span class="sr-only">Close</span>
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>

        <main class="dashboard-main min-h-screen p-4 transition-all duration-300 ease-out sm:p-5 lg:p-6"
            :class="{
                'lg:ml-64': isSidebarOpen && !isMobile(),
                'lg:ml-20': !isSidebarOpen && !isMobile()
            }" 
            style="padding-top: 5.5rem;">
            <div class="mx-auto w-full max-w-screen-2xl">
                {{ $slot }}
            </div>
        </main>

        <footer class="bg-gray-900 dark:bg-gray-950 border-t border-white/10 transition-all duration-300 ease-out"
                :class="{ 
                    'lg:ml-64': isSidebarOpen && !isMobile(),
                    'lg:ml-20': !isSidebarOpen && !isMobile()
                }">
            <x-dashboard-footer />
        </footer>

    </div>

    <script>
        // ✅ PERBAIKAN: Nonaktifkan Turbo untuk semua form di dashboard
        // Ini memastikan form submissions menggunakan full page reload
        // sehingga session flash messages bekerja dengan benar
        
        // Fungsi untuk menonaktifkan Turbo pada semua form
        function disableTurboForForms() {
            const dashboardForms = document.querySelectorAll('form');
            dashboardForms.forEach(form => {
                if (!form.hasAttribute('data-turbo')) {
                    form.setAttribute('data-turbo', 'false');
                }
            });
        }

        // Setup sekali di DOMContentLoaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', disableTurboForForms);
        } else {
            disableTurboForForms(); // DOM sudah siap
        }

        // Juga nonaktifkan Turbo saat form baru ditambahkan (untuk dynamic forms)
        // Gunakan once: true untuk mencegah multiple listeners (jika browser support)
        // Fallback: gunakan flag untuk mencegah duplikasi
        if (!window.dashboardTurboFormListenerAttached) {
            document.addEventListener('turbo:render', disableTurboForForms);
            window.dashboardTurboFormListenerAttached = true;
        }

        function dashboardState() {
            return {
                isSidebarOpen: window.innerWidth >= 1024,
                notificationOpen: false,
                notificationMessage: '',
                notificationType: 'success',
                modalOpen: false,
                modalType: 'success',
                modalTitle: '',
                modalMessage: '',

                init() {
                    console.log('🏠 Dashboard Alpine initialized');
                    this.setupResizeHandler();

                    // Cek pesan Flash (dari PHP Session)
                    // Gunakan $nextTick untuk memastikan DOM sudah siap sepenuhnya
                    this.$nextTick(() => {
                        this.checkForFlashMessages();
                    });

                    // ✅ PERBAIKAN: Setup Turbo event listeners hanya sekali
                    // Menggunakan flag untuk mencegah multiple listeners
                    if (!window.dashboardTurboListenersAttached) {
                        const checkFlash = () => {
                            setTimeout(() => {
                                this.checkForFlashMessages();
                            }, 100);
                        };

                        document.addEventListener('turbo:render', () => {
                            console.log('🎨 Turbo render - checking flash messages');
                            checkFlash();
                        });

                        document.addEventListener('turbo:load', () => {
                            console.log('📦 Turbo load - checking flash messages');
                            checkFlash();
                        });

                        window.dashboardTurboListenersAttached = true;
                    }
                },

                checkForFlashMessages() {
                    // Logika ini BENAR. Biarkan komponen ini yang mengambil pesan,
                    // karena dia yang bertanggung jawab menampilkannya.
                    const toastTrigger = document.getElementById('notification-trigger');
                    if (toastTrigger) {
                        const message = toastTrigger.getAttribute('data-message');
                        const type = toastTrigger.getAttribute('data-type');
                        
                        if (message) {
                            console.log('📢 Alpine menemukan pesan flash:', message);
                            this.showNotification(message, type);
                            toastTrigger.remove(); // Hapus agar tidak muncul 2x
                        }
                    }

                    // Cek Modal (sama seperti sebelumnya)
                    const modalTrigger = document.getElementById('modal-trigger');
                    if (modalTrigger) {
                        const title = modalTrigger.getAttribute('data-title');
                        const message = modalTrigger.getAttribute('data-message');
                        const type = modalTrigger.getAttribute('data-type');
                        
                        if (message) {
                            this.showModal(title, message, type);
                            modalTrigger.remove();
                        }
                    }
                },

                showNotification(message, type) {
                    // ... (logika sama seperti sebelumnya) ...
                    console.log('✅ Showing notification:', message);
                    this.notificationMessage = message;
                    this.notificationType = type;
                    this.notificationOpen = true;
                    
                    setTimeout(() => {
                        this.notificationOpen = false;
                    }, 4000);
                },

                showModal(title, message, type = 'success') {
                    // ... (logika sama seperti sebelumnya) ...
                    this.modalTitle = title;
                    this.modalMessage = message;
                    this.modalType = type;
                    this.modalOpen = true;
                },

                // ... (sisa fungsi helper isMobile, closeSidebar, dll tetap sama) ...
                isMobile() { return window.innerWidth < 1024; },
                closeSidebar() { if (this.isMobile()) { this.isSidebarOpen = false; } },
                setupResizeHandler() {
                    let resizeTimeout;
                    window.addEventListener('resize', () => {
                        clearTimeout(resizeTimeout);
                        resizeTimeout = setTimeout(() => {
                            this.isSidebarOpen = window.innerWidth >= 1024;
                        }, 100);
                    });
                }
            }
        }
    </script>
</body>
</html>
