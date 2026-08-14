<x-dashboard-layout>
    <x-slot:title>Tambah Jadwal Acara</x-slot:title>

    <div class="pb-6" x-data="{
        title: @js(old('title', '')),
        slug: @js(old('slug', '')),
        generateSlug() {
            this.slug = this.title.toLowerCase()
                .replace(/[^a-z0-9\s-]/g, '')
                .replace(/\s+/g, '-')
                .replace(/-+/g, '-')
                .trim();
        }
    }">
        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-8 pt-2">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                    Tambah Jadwal Acara
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Isi detail untuk jadwal penayangan program baru.
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
            <form action="{{ route('dashboard.jadwal-acara.store') }}" method="POST">
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

                    <div class="grid gap-6 md:grid-cols-2">

                        {{-- Nama Acara --}}
                        <div class="md:col-span-2">
                            <label for="title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Nama Acara <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="title"
                                   id="title"
                                   x-model="title"
                                   @input.debounce.500ms="generateSlug()"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                   placeholder="Contoh: Berita Pagi"
                                   required
                                   autofocus>
                            @error('title')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Slug URL --}}
                        <div class="md:col-span-2">
                            <label for="slug" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Slug URL <span class="text-red-500">*</span>
                            </label>
                            <input type="text"
                                   name="slug"
                                   id="slug"
                                   x-model="slug"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white font-mono text-xs"
                                   required>
                            @error('slug')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Pukul Mulai --}}
                        <div>
                            <label for="start_time" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Pukul Mulai <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <div class="absolute inset-y-0 end-0 top-0 flex items-center pe-3.5 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd" d="M2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10S2 17.523 2 12Zm11-4a1 1 0 1 0-2 0v4a1 1 0 0 0 .293.707l3 3a1 1 0 0 0 1.414-1.414L13 11.586V8Z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <input type="time"
                                       name="start_time"
                                       id="start_time"
                                       class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                       required
                                       value="{{ old('start_time') }}">
                            </div>
                            @error('start_time')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Pukul Selesai --}}
                        <div>
                            <label for="end_time" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Pukul Selesai
                            </label>
                            <input type="time"
                                   name="end_time"
                                   id="end_time"
                                   class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                                   value="{{ old('end_time') }}"
                                   aria-describedby="end_time_help">
                            <p id="end_time_help" class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                Opsional. Jika kosong, durasi tayang dianggap satu jam.
                            </p>
                            @error('end_time')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Hari Penayangan --}}
                        <div>
                            <label for="jadwal_category_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Hari Penayangan <span class="text-red-500">*</span>
                            </label>
                            <select name="jadwal_category_id"
                                    id="jadwal_category_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                    required>
                                <option value="">Pilih Hari...</option>
                                @foreach($jadwalCategories as $day)
                                    <option value="{{ $day->id }}" {{ old('jadwal_category_id') == $day->id ? 'selected' : '' }}>
                                        {{ $day->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jadwal_category_id')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Kategori Acara --}}
                        <div>
                            <label for="broadcast_category_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Kategori Acara <span class="text-red-500">*</span>
                            </label>
                            <select name="broadcast_category_id"
                                    id="broadcast_category_id"
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                    required>
                                <option value="">Pilih Kategori...</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('broadcast_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('broadcast_category_id')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Status Penayangan --}}
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Status Penayangan <span class="text-red-500">*</span>
                            </label>
                            <div class="flex flex-col sm:flex-row gap-3">
                                {{-- Radio Aktif --}}
                                <div class="flex items-center ps-4 border border-gray-200 rounded dark:border-gray-700 bg-gray-50 dark:bg-gray-700 flex-1">
                                    <input id="active_yes"
                                           type="radio"
                                           value="1"
                                           name="is_active"
                                           {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="active_yes" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300 cursor-pointer">
                                        Aktif (Tayang)
                                    </label>
                                </div>

                                {{-- Radio Non-Aktif --}}
                                <div class="flex items-center ps-4 border border-gray-200 rounded dark:border-gray-700 bg-gray-50 dark:bg-gray-700 flex-1">
                                    <input id="active_no"
                                           type="radio"
                                           value="0"
                                           name="is_active"
                                           {{ old('is_active') == '0' ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="active_no" class="w-full py-3 ms-2 text-sm font-medium text-gray-900 dark:text-gray-300 cursor-pointer">
                                        Non-Aktif
                                    </label>
                                </div>
                            </div>
                            @error('is_active')
                                <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="dashboard-form-actions flex items-center justify-between gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 rounded-b-lg">
                    <a href="{{ route('dashboard.jadwal-acara.index') }}"
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
