<x-dashboard-layout>
    <x-slot:title>Tambah FAQ Kunjungan</x-slot:title>

    <div class="pb-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-8 pt-2">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                    Tambah FAQ Kunjungan
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Buat pertanyaan baru seputar kunjungan.
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
            <form action="{{ route('dashboard.info-kunjungan-faq.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-6">
                    
                    {{-- Pertanyaan --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Pertanyaan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="question" value="{{ old('question') }}" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
                            placeholder="Contoh: Bagaimana cara mengajukan kunjungan?" required>
                        @error('question') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Jawaban --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Jawaban <span class="text-red-500">*</span>
                        </label>
                        <textarea name="answer" rows="5" 
                            class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
                            placeholder="Tuliskan jawaban lengkap di sini..." required>{{ old('answer') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Tekan Enter untuk membuat paragraf baru.</p>
                        @error('answer') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                        {{-- Urutan --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Urutan Tampilan
                            </label>
                            <input type="number" name="order" value="{{ old('order', 0) }}" min="0" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Angka kecil (0, 1, 2) akan tampil lebih dulu.</p>
                        </div>

                        {{-- Status Checkbox --}}
                        <div class="flex items-center md:pt-7">
                            <label class="relative inline-flex items-center cursor-pointer p-2 border border-gray-200 dark:border-gray-600 rounded-lg w-full hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[12px] after:left-[12px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Publikasikan (Aktif)</span>
                            </label>
                        </div>
                    </div>

                </div>

                {{-- Footer Action --}}
                <div class="dashboard-form-actions flex items-center justify-between gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                    <a href="{{ route('dashboard.info-kunjungan-faq.index') }}" 
                       class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 hover:text-gray-900 focus:z-10 focus:ring-4 focus:ring-gray-200 dark:focus:ring-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors shadow-sm">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-dashboard-layout>
