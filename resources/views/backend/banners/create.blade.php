<x-dashboard-layout>
    <x-slot:title>Tambah Banner Beranda</x-slot:title>

    <div class="pb-6">
        {{-- HEADER HALAMAN --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-8 pt-2">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                    Buat Banner Baru
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Tambahkan banner baru untuk carousel halaman utama.
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
            <form action="{{ route('banners.store') }}" method="POST" enctype="multipart/form-data" novalidate
                  x-data="{
                      imagePreview: null,
                      handleFileSelect(event) {
                          const file = event.target.files[0];
                          if (!file) return;
                          
                          // Validasi ukuran (5MB)
                          if (file.size > 5 * 1024 * 1024) {
                              alert('Ukuran file terlalu besar. Maksimal 5MB.');
                              event.target.value = '';
                              return;
                          }
                          
                          // Preview
                          const reader = new FileReader();
                          reader.onload = (e) => { this.imagePreview = e.target.result };
                          reader.readAsDataURL(file);
                      }
                  }">
                @csrf
                
                <div class="p-6 space-y-6">
                    
                    {{-- Error Summary --}}
                    @if ($errors->any())
                        <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-red-900/30 dark:text-red-400 border border-red-200 dark:border-red-800" role="alert">
                            <div class="font-medium mb-1">Oops! Ada beberapa kesalahan:</div>
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Judul --}}
                    <div>
                        <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Judul Banner <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
                            placeholder="Contoh: Berita Terkini TVRI" required>
                        @error('title') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Subtitle --}}
                    <div>
                        <label for="subtitle" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Subtitle <span class="text-red-500">*</span>
                        </label>
                        <textarea name="subtitle" id="subtitle" rows="3" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
                            placeholder="Deskripsi singkat untuk banner..." required>{{ old('subtitle') }}</textarea>
                        @error('subtitle') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Grid 2 Kolom untuk Link & Button Text --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="link" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Tautan Tujuan (URL) <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="link" id="link" value="{{ old('link') }}" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
                                placeholder="https://..." required>
                            @error('link') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label for="button_text" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Teks Tombol
                            </label>
                            <input type="text" name="button_text" id="button_text" value="{{ old('button_text', 'Selengkapnya') }}" 
                                class="bg-gray-50 underline border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" 
                                placeholder="Contoh: Lihat Selengkapnya">
                            @error('button_text') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Upload Gambar --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Gambar Banner (21:9) <span class="text-red-500">*</span>
                        </label>
                        
                        {{-- Preview Box --}}
                        <div class="mb-4">
                            <div class="relative w-full max-w-lg aspect-video bg-gray-100 dark:bg-gray-700 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600 flex items-center justify-center overflow-hidden">
                                <template x-if="imagePreview">
                                    <img :src="imagePreview" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!imagePreview">
                                    <div class="text-center p-4">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Klik tombol di bawah untuk upload</p>
                                    </div>
                                </template>
                            </div>
                        </div>

                        <input type="file" name="image" id="image" @change="handleFileSelect($event)" accept="image/jpeg,image/png,image/webp" 
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" required>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Format: JPG, PNG, WebP (Maks. 5MB). Rasio disarankan 21:9.</p>
                        @error('image') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Grid Urutan & Status --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
                        <div>
                            <label for="order" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Urutan Tampil</label>
                            <input type="number" name="order" id="order" min="0" value="{{ old('order', 0) }}" 
                                class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white" required>
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Angka lebih kecil tampil lebih dulu.</p>
                        </div>
                        
                        <div class="flex items-center pt-6">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', 1) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-600 peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-500 peer-checked:bg-blue-600"></div>
                                <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Status Aktif</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="dashboard-form-actions flex items-center justify-between gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 rounded-b-lg">
                    <a href="{{ route('banners.index') }}" 
                       class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                        Batal
                    </a>
                    <button type="submit" 
                            class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-dashboard-layout>
