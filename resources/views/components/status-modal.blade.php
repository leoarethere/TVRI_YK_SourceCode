<div x-show="modalOpen"
     x-cloak
     role="dialog"
     aria-modal="true"
     aria-labelledby="dashboard-status-modal-title"
     aria-describedby="dashboard-status-modal-message"
     @keydown.escape.window="if (modalOpen) closeModal()"
     class="fixed inset-0 z-[60] overflow-y-auto bg-gray-900/75 backdrop-blur-sm flex items-center justify-center p-4"
     style="display: none;">

    <div x-show="modalOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform scale-95"
         x-transition:enter-end="opacity-100 transform scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 transform scale-100"
         x-transition:leave-end="opacity-0 transform scale-95"
         @click.away="closeModal()"
         class="relative bg-white rounded-lg shadow-xl dark:bg-gray-800 max-w-sm w-full text-center p-6 border border-gray-100 dark:border-gray-700">

        {{-- Ikon Sukses (Centang Hijau) --}}
        <div x-show="modalType === 'success'" class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 dark:bg-green-900/30 mb-5">
            <svg class="h-8 w-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>

        {{-- Ikon Error (Silang Merah) --}}
        <div x-show="modalType === 'error'" class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 dark:bg-red-900/30 mb-5">
            <svg class="h-8 w-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>

        {{-- Judul & Pesan --}}
        <h3 id="dashboard-status-modal-title" class="text-xl font-bold text-gray-900 dark:text-white mb-2" x-text="modalTitle"></h3>
        <p id="dashboard-status-modal-message" class="text-sm text-gray-500 dark:text-gray-400 mb-6 leading-relaxed" x-text="modalMessage"></p>

        {{-- Tombol OK --}}
        <button x-ref="modalDismiss"
                x-effect="if (modalOpen) $nextTick(() => $refs.modalDismiss.focus())"
                @click="closeModal()"
                type="button"
                class="w-full inline-flex justify-center items-center px-4 py-2.5 text-sm font-medium text-white rounded-lg focus:outline-none focus:ring-4 transition-colors shadow-sm"
                :class="modalType === 'success' ? 'bg-green-600 hover:bg-green-700 focus:ring-green-300 dark:focus:ring-green-800' : 'bg-red-600 hover:bg-red-700 focus:ring-red-300 dark:focus:ring-red-800'">
            OK, Mengerti
        </button>
    </div>
</div>
