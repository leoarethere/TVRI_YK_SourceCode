<x-dashboard-layout>
    <x-slot:title>Tambah Program Siaran</x-slot:title>

    <div class="pb-6"
        {{-- Alpine.js untuk generate slug --}}
        x-data="{
            title: @js(old('title', '')),
            slug: @js(old('slug', '')),

            generateSlug() {
                this.slug = this.title
                    .toLowerCase()
                    .replace(/[^a-z0-9\s-]/g, '')
                    .replace(/\s+/g, '-')
                    .replace(/-+/g, '-')
                    .trim();
            }
        }"
    >
        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-8 pt-2">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                    Tambah Program Siaran
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Isi detail untuk rilis, siaran, atau pengumuman baru.
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
            <form action="{{ route('dashboard.penyiaran.store') }}" method="POST" enctype="multipart/form-data">
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

                    {{-- Judul Penyiaran --}}
                    <div>
                        <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Nama Siaran / Judul <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" x-model="title" @input.debounce.500ms="generateSlug()"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Masukkan judul penyiaran..." required autofocus>
                        @error('title') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Slug --}}
                    <div>
                        <label for="slug" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Slug URL <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="slug" id="slug" x-model="slug"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white font-mono" required>
                        @error('slug') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Kategori Penyiaran --}}
                        <div>
                            <label for="broadcast_category_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <select name="broadcast_category_id" id="broadcast_category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                <option value="">Pilih Kategori...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('broadcast_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('broadcast_category_id') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Link Youtube --}}
                        <div>
                            <label for="youtube_link" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Link Youtube (Opsional)
                            </label>
                            <input type="url" name="youtube_link" id="youtube_link" value="{{ old('youtube_link') }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="https://youtube.com/...">
                            @error('youtube_link') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Status Produksi --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Status Produksi <span class="text-red-500">*</span>
                        </label>
                        <div class="flex flex-col sm:flex-row gap-3">
                            {{-- Radio Aktif --}}
                            <div class="flex items-center ps-4 border border-gray-200 rounded dark:border-gray-700 bg-gray-50 dark:bg-gray-700 flex-1">
                                <input checked id="active_yes" type="radio" value="1" name="is_active"
                                    {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="active_yes" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300 cursor-pointer">
                                    Sedang Tayang (On-Going)
                                </label>
                            </div>

                            {{-- Radio Tidak Aktif --}}
                            <div class="flex items-center ps-4 border border-gray-200 rounded dark:border-gray-700 bg-gray-50 dark:bg-gray-700 flex-1">
                                <input id="active_no" type="radio" value="0" name="is_active"
                                    {{ old('is_active') == '0' ? 'checked' : '' }}
                                    class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                <label for="active_no" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300 cursor-pointer">
                                    Selesai (Completed)
                                </label>
                            </div>
                        </div>
                    </div>

                    {{-- Sinopsis --}}
                    <div>
                        <label for="synopsis" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Sinopsis (Opsional)
                        </label>
                        <textarea name="synopsis" id="synopsis" rows="4"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Ringkasan singkat...">{{ old('synopsis') }}</textarea>
                        @error('synopsis') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Poster --}}
                    <div x-data="{
                        preview: null,
                        handleFile(event) {
                            const file = event.target.files[0];
                            if (!file) return;
                            if (file.size > 10 * 1024 * 1024) {
                                alert('Ukuran file terlalu besar. Maksimal 10MB.');
                                event.target.value = '';
                                return;
                            }
                            this.preview = URL.createObjectURL(file);
                        }
                    }">
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Poster (Opsional)
                        </label>

                        {{-- Preview --}}
                        <div class="mb-3" x-show="preview">
                            <div class="relative w-32 h-40 rounded-lg overflow-hidden border border-gray-300 dark:border-gray-600">
                                <img :src="preview" class="w-full h-full object-cover">
                                <button type="button" @click="preview = null; document.getElementById('poster').value = ''" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>

                        <input type="file" name="poster" id="poster" @change="handleFile($event)" accept="image/png, image/jpeg, image/jpg, image/webp"
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">PNG, JPG, WEBP (MAX. 10MB).</p>
                        @error('poster') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tanggal Publikasi --}}
                    <div>
                        <label for="published_at" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Tanggal Publikasi
                        </label>
                        <input type="datetime-local" name="published_at" id="published_at" value="{{ old('published_at', now()->format('Y-m-d\TH:i')) }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Akan diisi otomatis saat dipublikasikan jika dibiarkan kosong.</p>
                    </div>

                </div>

                {{-- Action Buttons --}}
                <div class="dashboard-form-actions flex items-center justify-between gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 rounded-b-lg">
                    <a href="{{ route('dashboard.penyiaran.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                        Batal
                    </a>
                    <div class="flex gap-3">
                        <button type="submit" name="action" value="draft" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                            Simpan Draft
                        </button>
                        <button type="submit" name="action" value="publish" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors">
                            Publikasikan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</x-dashboard-layout>
