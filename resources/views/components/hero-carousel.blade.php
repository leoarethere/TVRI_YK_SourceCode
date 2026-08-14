@props(['slides' => []])

{{-- Early return jika tidak ada slides untuk mencegah error --}}
@if(empty($slides))
    <div class="bg-gray-100 dark:bg-gray-800 rounded-2xl h-96 flex items-center justify-center border border-gray-200 dark:border-gray-700">
        <div class="text-center text-gray-500 dark:text-gray-400">
            <svg class="w-12 h-12 mx-auto mb-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
            <p>Belum ada banner yang ditambahkan.</p>
        </div>
    </div>
    @php return; @endphp
@endif

<section
    x-data="{
        slides: @js($slides),
        activeSlide: 0,
        progress: 0,
        autoplayInterval: null,
        autoplayDuration: 5000,

        resetAutoplay() {
            this.progress = 0;
            this.startAutoplay();
        },

        startAutoplay() {
            clearInterval(this.autoplayInterval);
            if (this.slides.length <= 1) {
                this.progress = 100;
                return;
            }
            this.autoplayInterval = setInterval(() => {
                this.progress += 1;
                if (this.progress >= 100) {
                    this.nextSlide();
                }
            }, this.autoplayDuration / 100);
        },

        stopAutoplay() {
            clearInterval(this.autoplayInterval);
            this.autoplayInterval = null;
        },

        nextSlide() {
            this.activeSlide = (this.activeSlide + 1) % this.slides.length;
            this.progress = 0;
        },

        selectSlide(index) {
            this.activeSlide = index;
            this.resetAutoplay();
        },

        destroy() {
            this.stopAutoplay();
        }
    }"
    x-init="startAutoplay()"
    @mouseenter="stopAutoplay()"
    @mouseleave="startAutoplay()"
    {{-- ✅ PERBAIKAN: Tinggi minimal 400px untuk mobile agar tidak gepeng, aspect-ratio 21/9 untuk desktop --}}
    class="relative w-full min-h-[400px] md:min-h-0 md:aspect-[21/9] overflow-hidden rounded-2xl shadow-lg group bg-gray-900"
>

    {{-- Slides Container --}}
    <template x-for="(slide, index) in slides" :key="index">
        <div
            x-show="activeSlide === index"
            x-transition:enter="transition ease-out duration-1000"
            x-transition:enter-start="opacity-0 scale-105"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-700"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-105"
            class="absolute inset-0"
        >
            {{-- Background Image --}}
            <img :src="slide.image"
                 class="h-full w-full object-cover"
                 :alt="slide.title"
                 loading="lazy">

            {{-- Gradient Overlay (Lebih gelap di bawah untuk teks) --}}
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/90 via-gray-900/40 to-transparent"></div>

            {{-- Content Wrapper --}}
            <div class="absolute inset-0 flex items-center justify-start p-6 sm:p-10 md:p-16 lg:p-24">
                <div class="max-w-2xl text-left"
                     x-show="activeSlide === index"
                     x-transition:enter="transition ease-out duration-700 delay-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0">

                    {{-- Heading: Ukuran font responsif --}}
                    <h2 class="mb-3 text-2xl font-extrabold tracking-tight text-white sm:text-2xl md:text-3xl lg:text-4xl drop-shadow-lg"
                        x-text="slide.title">
                    </h2>

                    {{-- Subtitle: Ukuran font dan line-clamp responsif --}}
                    <p class="mb-6 text-sm font-medium text-gray-200 sm:text-base md:text-lg lg:text-lg line-clamp-3 sm:line-clamp-2 drop-shadow-lg max-w-xl"
                       x-text="slide.subtitle">
                    </p>

                    {{-- Button --}}
                    <a :href="slide.link"
                       class="inline-flex items-center gap-2 px-6 py-3 text-sm font-semibold text-white transition-all duration-200 bg-blue-600 rounded-xl hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow-lg hover:shadow-blue-500/30">
                        <span x-text="slide.button_text || 'Selengkapnya'"></span>
                        {{-- Heroicon: Arrow Right --}}
                        <svg class="w-4 h-4 transition-transform duration-200 group-hover:translate-x-1" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </template>

    {{-- Bottom Navigation (Indicators) --}}
    <div class="absolute bottom-0 left-0 right-0 z-20 w-full bg-gradient-to-t from-gray-900/80 to-transparent pt-12 pb-4">
        <div class="flex justify-center items-end px-4 gap-2 sm:gap-4">
            <template x-for="(slide, index) in slides" :key="index">
                <button
                    type="button"
                    @click="selectSlide(index)"
                    class="group relative flex flex-col justify-end w-full max-w-[80px] sm:max-w-[120px] focus:outline-none"
                    :aria-label="'Go to slide ' + (index + 1)">

                    {{-- Label Judul Kecil (Hidden on very small screens) --}}
                    <span class="hidden sm:block mb-2 text-xs font-medium truncate transition-colors duration-300 text-center"
                          :class="activeSlide === index ? 'text-white' : 'text-gray-400 group-hover:text-gray-300'"
                          x-text="slide.title">
                    </span>

                    {{-- Progress Bar Container --}}
                    <div class="h-1 w-full rounded-full bg-gray-700/50 overflow-hidden backdrop-blur-sm">
                        {{-- Active Progress --}}
                        <div class="h-full bg-blue-500 rounded-full transition-all duration-100 ease-linear"
                             :style="`width: ${activeSlide === index ? progress : (activeSlide > index ? 100 : 0)}%`">
                        </div>
                    </div>
                </button>
            </template>
        </div>
    </div>
</section>
