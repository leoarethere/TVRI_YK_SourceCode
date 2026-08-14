<div x-show="deleteModal"
     x-cloak
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0 transform scale-95"
     x-transition:enter-end="opacity-100 transform scale-100"
     class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/75 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full overflow-hidden" @click.away="closeDeleteModal()">
        <div class="p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Konfirmasi Hapus</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-6">
                {{ $label }} <span class="font-medium text-gray-900 dark:text-white" x-text="deleteTitle"></span> akan dihapus permanen.
            </p>
            <div class="flex justify-end gap-3">
                <button type="button" @click="closeDeleteModal()" class="px-4 py-2 text-sm text-gray-700 bg-gray-100 rounded-lg hover:bg-gray-200 transition">Batal</button>
                <form :action="`{{ $routePrefix }}/${deleteId}`" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-4 py-2 text-sm text-white bg-red-600 rounded-lg hover:bg-red-700 transition">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
