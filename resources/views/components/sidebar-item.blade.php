@props(['href', 'active' => false])

<li>
    <a href="{{ $href }}"
       aria-label="{{ trim(strip_tags((string) $slot)) }}"
       :class="{ 'justify-center': !isSidebarOpen }"
       @class([
           'flex items-center p-2 text-base font-medium rounded-lg group transition-all duration-300',
           'bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white' => $active,
           'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700' => !$active,
       ])>

        {{-- Ikon --}}
        <div @class([
            'flex-shrink-0 transition-transform duration-300 group-hover:scale-110',
            'text-gray-900 dark:text-white' => $active,
            'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' => !$active,
        ])>
            {{ $icon }}
        </div>

        {{-- Teks --}}
        {{-- Durasi diubah ke 300ms agar sinkron dengan sidebar width --}}
        <span class="flex-1 ml-3 whitespace-nowrap overflow-hidden"
              x-show="isSidebarOpen"
              x-transition:enter="transition ease-out duration-300"
              x-transition:enter-start="opacity-0 -translate-x-2"
              x-transition:enter-end="opacity-100 translate-x-0"
              x-transition:leave="transition ease-in duration-300"
              x-transition:leave-start="opacity-100 translate-x-0"
              x-transition:leave-end="opacity-0 -translate-x-2">
            {{ $slot }}
        </span>
    </a>
</li>
