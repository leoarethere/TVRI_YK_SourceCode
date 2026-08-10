<footer class="bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800 transition-colors duration-300">
    <div class="container mx-auto px-4 py-8 sm:px-6 sm:py-10 lg:px-8 lg:py-12">
        
        {{-- Layout Utama: Flexbox untuk Desktop --}}
        <div class="space-y-8 lg:flex lg:justify-between lg:space-y-0">
            
            {{-- 1. Bagian Brand / Logo (KIRI) --}}
            <div class="max-w-xs flex-shrink-0 space-y-3 md:max-w-sm">
                <a href="/" class="flex items-center gap-2">
                    {{-- Logo Gelap (Light Mode) --}}
                    <img src="{{ asset('img/logolight.png') }}" 
                        class="h-8 sm:h-9 md:h-10 w-auto block dark:hidden" 
                        alt="Logo TVRI D.I. Yogyakarta" />

                    {{-- Logo Terang (Dark Mode) --}}
                    <img src="{{ asset('img/logodark.png') }}" 
                        class="h-8 sm:h-9 md:h-10 w-auto hidden dark:block" 
                        alt="Logo TVRI D.I. Yogyakarta" />
                    
                    {{-- [RESPONSIVE FONT] text-lg di mobile, text-xl di desktop --}}
                    <span class="self-center text-lg md:text-xl font-bold text-gray-900 dark:text-white whitespace-nowrap">
                        Media Pemersatu Bangsa
                    </span>
                </a>
                
                {{-- [RESPONSIVE FONT] text-sm di mobile, text-base di desktop --}}
                <p class="text-sm md:text-base text-gray-500 dark:text-gray-400 leading-relaxed">
                    Lembaga Penyiaran Publik Televisi Republik Indonesia Stasiun D.I. Yogyakarta.
                </p>
            </div>

            {{-- 2. Bagian Link Navigasi (KANAN) --}}
            <div class="grid grid-cols-2 gap-6 sm:grid-cols-3 sm:gap-10 lg:gap-14">
                
                {{-- Kolom 1 --}}
                <div class="md:grid md:grid-cols-1 md:gap-8">
                    <div>
                        <h3 class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white tracking-wider uppercase">
                            Informasi Publik
                        </h3>
                        <ul role="list" class="mt-3 space-y-3">
                            <li>
                                <a href="{{ route('ppid.index') }}" class="text-sm md:text-base text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors">
                                    PPID
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('info-rb.index') }}" class="text-sm md:text-base text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors">
                                    Reformasi Birokrasi
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('info-magang.index') }}" class="text-sm md:text-base text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors">
                                    Informasi Magang
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Kolom 2 --}}
                <div class="md:grid md:grid-cols-1 md:gap-8">
                    <div>
                        <h3 class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white tracking-wider uppercase">
                            Program & Acara
                        </h3>
                        <ul role="list" class="mt-3 space-y-3">
                            <li>
                                <a href="{{ route('penyiaran.index') }}" class="text-sm md:text-base text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors">
                                    Program Siaran
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('publikasi.jadwal') }}" class="text-sm md:text-base text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors">
                                    Jadwal Acara
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('postingan.index') }}" class="text-sm md:text-base text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors">
                                    Postingan
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Kolom 3 --}}
                <div class="col-span-2 sm:col-span-1 md:grid md:grid-cols-1 md:gap-8">
                    <div>
                        <h3 class="text-xs sm:text-sm font-semibold text-gray-900 dark:text-white tracking-wider uppercase">
                            Tentang Kami
                        </h3>
                        <ul role="list" class="mt-3 space-y-3">
                            <li>
                                <a href="{{ route('sejarah') }}" class="text-sm md:text-base text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors">
                                    Sejarah
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('visi-misi') }}" class="text-sm md:text-base text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors">
                                    Visi & Misi
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('tugas-fungsi') }}" class="text-sm md:text-base text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors">
                                    Tugas & Fungsi
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="mt-8 border-t border-gray-200 pt-6 dark:border-gray-800">
            <div class="flex flex-col space-y-2">
                <p class="text-sm md:text-base text-gray-400 dark:text-gray-500 text-center">
                    &copy; {{ date('Y') }} TVRI Stasiun D.I. Yogyakarta. Hak cipta dilindungi.
                </p>
                <p class="text-sm md:text-base text-gray-400 dark:text-gray-500 text-center leading-relaxed">
                    <span class="font-mono text-blue-500 font-bold mr-1">&lt;/&gt;</span>
                    Didesain dan dikembangkan oleh
                    <span class="inline-block">
                        <a href="https://www.instagram.com/leoarethere/" target="_blank" rel="noopener noreferrer" class="underline hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            Leonardo Putra Susanto
                        </a>
                        &amp;
                        <a href="https://www.instagram.com/destywahyu01/" target="_blank" rel="noopener noreferrer" class="underline hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                            Desty Wahyu Anjani
                        </a>
                    </span>
                </p>
            </div>
        </div>
    </div>
</footer>
