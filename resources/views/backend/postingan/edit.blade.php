<x-dashboard-layout>
    <x-slot:title>Edit Postingan</x-slot:title>

    {{-- CSS Trix --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <style>
        trix-toolbar [data-trix-button-group="file-tools"] { display: none; }
        .dark trix-editor { background-color: #374151; color: white; border-color: #4b5563; }
        .dark trix-toolbar { background-color: #f3f4f6; border-radius: 0.5rem; margin-bottom: 0.5rem; }
        trix-editor { min-height: 300px; }
        trix-editor ul { list-style-type: disc !important; padding-left: 1.5rem !important; margin-bottom: 1rem !important; }
        trix-editor ol { list-style-type: decimal !important; padding-left: 1.5rem !important; margin-bottom: 1rem !important; }
        trix-editor li { margin-bottom: 0.5rem; }
        trix-editor blockquote { border-left: 4px solid #e5e7eb; padding-left: 1rem; margin-left: 0; font-style: italic; color: #4b5563; }
        .dark trix-editor blockquote { border-color: #4b5563; color: #9ca3af; }
    </style>
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    <div class="pb-6"
        x-data="{
            title: @js(old('title', $post->title)),
            slug: @js(old('slug', $post->slug))
        }"
    >
        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-8 pt-2">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                    Edit Postingan
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Perbarui konten artikel Anda di sini.
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
            <form action="{{ route('dashboard.postingan.update', $post) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

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
                            Judul Postingan <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" x-model="title"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
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
                        {{-- Kategori --}}
                        <div>
                            <label for="category_id" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Kategori <span class="text-red-500">*</span>
                            </label>
                            <select name="category_id" id="category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('category_id', $post->category_id) == $category->id)>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Link Sumber --}}
                        <div>
                            <label for="link_postingan" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Link Sumber / Eksternal (Opsional)
                            </label>
                            <input type="url" name="link_postingan" id="link_postingan" value="{{ old('link_postingan', $post->link_postingan) }}"
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                                placeholder="https://...">
                            @error('link_postingan') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Konten Body --}}
                    <div>
                        <label for="body" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Konten Postingan <span class="text-red-500">*</span>
                        </label>
                        <input id="body" type="hidden" name="body" value="{{ old('body', $post->body) }}">
                        <trix-editor input="body" class="trix-content bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white min-h-[300px]" placeholder="Tulis konten di sini..."></trix-editor>
                        @error('body') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Featured Image --}}
                    <div x-data="{
                        preview: @js($post->featured_image ? Storage::url($post->featured_image) : null),
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
                            Gambar Unggulan (Opsional)
                        </label>

                        {{-- Preview --}}
                        <div class="mb-3" x-show="preview">
                            <div class="relative w-full max-w-md aspect-video rounded-lg overflow-hidden border border-gray-300 dark:border-gray-600">
                                <img :src="preview" class="w-full h-full object-cover">
                                <button type="button" @click="preview = null; document.getElementById('featured_image').value = ''" class="absolute top-2 right-2 bg-red-500 text-white rounded-full p-1.5 hover:bg-red-600 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>

                        <input type="file" name="featured_image" id="featured_image" @change="handleFile($event)" accept="image/png, image/jpeg, image/jpg, image/webp"
                            class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Biarkan kosong jika tidak ingin mengubah gambar.</p>
                        @error('featured_image') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Excerpt --}}
                    <div>
                        <label for="excerpt" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Ringkasan (Opsional)
                        </label>
                        <textarea name="excerpt" id="excerpt" rows="3"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"
                            placeholder="Ringkasan singkat...">{{ old('excerpt', $post->excerpt) }}</textarea>
                    </div>

                    {{-- Tanggal Publikasi --}}
                    <div>
                        <label for="published_at" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Tanggal Publikasi (Opsional)
                        </label>
                        <input type="datetime-local" name="published_at" id="published_at" value="{{ old('published_at', optional($post->published_at)->format('Y-m-d\TH:i')) }}"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Akan diisi otomatis saat dipublikasikan jika dibiarkan kosong.</p>
                    </div>

                </div>

                {{-- Action Buttons --}}
                <div class="dashboard-form-actions flex items-center justify-between gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 rounded-b-lg">
                    <a href="{{ route('dashboard.postingan.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                        Batal
                    </a>
                    <div class="flex gap-3">
                        <button type="submit" name="action" value="draft" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                            Simpan Draft
                        </button>
                        <button type="submit" name="action" value="publish" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors">
                            Perbarui & Publikasikan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener("trix-file-accept", function(event) { event.preventDefault(); });
    </script>
</x-dashboard-layout>
