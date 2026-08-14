<section 
    x-data="{
        slides: [
            { image: 'https://images.unsplash.com/photo-1488190211105-8b0e65b80b4e?q=80&w=2070&auto=format&fit=crop' },
            { image: 'https://images.unsplash.com/photo-1499750310107-5fef28a66643?q=80&w=2070&auto=format&fit=crop' },
            { image: 'https://images.unsplash.com/photo-1457369804613-52c61a468e7d?q=80&w=2070&auto=format&fit=crop' },
            { image: 'https://images.unsplash.com/photo-1522204523234-8729aa6e3d5f?q=80&w=2070&auto=format&fit=crop' }
        ],
        activeSlide: 0,
        autoplay: null,
        startAutoplay() {
            this.stopAutoplay();
            this.autoplay = setInterval(() => {
                this.activeSlide = (this.activeSlide + 1) % this.slides.length;
            }, 2000);
        },
        stopAutoplay() {
            clearInterval(this.autoplay);
            this.autoplay = null;
        },
        destroy() {
            this.stopAutoplay();
        }
    }"
    x-init="startAutoplay()"
    @mouseenter="stopAutoplay()"
    @mouseleave="startAutoplay()"
    {{-- 
        PERBAIKAN RESPONSIVITAS: 
        1. min-h-[50vh]: Menggunakan 50% tinggi viewport di mobile agar pas.
        2. md:min-h-[400px]: Tinggi minimal fix di tablet.
        3. md:aspect-[21/9]: Rasio lebar di desktop.
    --}}
    class="relative w-full min-h-[50vh] md:min-h-[400px] md:aspect-[21/9] overflow-hidden rounded-2xl shadow-lg group bg-gray-900"
>
    {{-- Slides Container --}}
    <template x-for="(slide, index) in slides" :key="index">
        <div 
            x-show="activeSlide === index"
            x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-105"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-1000"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-105"
            class="absolute inset-0 z-0"
        >
            <img :src="slide.image" 
                 class="w-full h-full object-cover" 
                 alt="Hero Background">
            
            {{-- Gradient Overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/90 via-gray-900/60 to-gray-900/30"></div>
        </div>
    </template>

    {{-- Content Container --}}
    <div class="absolute inset-0 flex flex-col items-center justify-center text-center px-4 sm:px-6 lg:px-8 z-10">
        <div class="max-w-3xl w-full space-y-6 md:space-y-8 animate-fade-in-up">
            
            {{-- Heading & Description --}}
            <div class="space-y-3 sm:space-y-4">
                {{-- Typography di-tweak: text-3xl di mobile, naik ke 4xl dan 5xl di layar lebih besar --}}
                <h1 class="text-3xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-white drop-shadow-lg leading-tight">
                    Apa yang kamu cari?
                </h1>
                <p class="text-sm sm:text-base md:text-lg text-gray-200 font-medium max-w-2xl mx-auto drop-shadow-md leading-relaxed px-2">
                    Jelajahi postingan terbaru, dan beragam dokumentasi di TVRI D.I. Yogyakarta
                </p>
            </div>
            
            {{-- Search Form --}}
            <form action="{{ route('postingan.index') }}" method="GET" class="w-full max-w-2xl mx-auto">
                @foreach(['category', 'author', 'sort'] as $param)
                    @if(request($param))
                        <input type="hidden" name="{{ $param }}" value="{{ request($param) }}">
                    @endif
                @endforeach
                
                {{-- Container Input: Flex Column di Mobile, Row di Tablet+ --}}
                <div class="relative flex flex-col sm:flex-row gap-3 p-2 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl shadow-2xl transition-all duration-300 focus-within:bg-white/20 focus-within:border-white/40">
                    <div class="relative flex-grow">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none text-gray-400">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                        {{-- Input Field --}}
                        <input 
                            type="search" 
                            name="search" 
                            id="hero-search" 
                            value="{{ request('search') }}" 
                            class="block w-full py-3 sm:py-3.5 pl-11 pr-4 text-sm sm:text-base text-gray-900 bg-white rounded-xl border-0 ring-1 ring-gray-200 focus:ring-2 focus:ring-blue-500 placeholder:text-gray-500 dark:bg-gray-800 dark:text-white dark:ring-gray-700 dark:placeholder:text-gray-400 shadow-sm transition-all" 
                            placeholder="Cari topik atau kata kunci..." 
                            autocomplete="off"
                            required
                        >
                    </div>
                    
                    {{-- Submit Button --}}
                    <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-6 py-3 sm:py-3.5 text-sm sm:text-base font-bold text-white transition-all duration-200 bg-blue-600 rounded-xl hover:bg-blue-700 active:scale-95 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg hover:shadow-blue-500/30 whitespace-nowrap">
                        Cari
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
