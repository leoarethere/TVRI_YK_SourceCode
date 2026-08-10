<x-dashboard-layout>
    <x-slot:title>Edit Berita</x-slot:title>

    {{-- CSS Trix --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <style>
        trix-toolbar [data-trix-button-group="file-tools"] { display: none; }
        .dark trix-editor { background-color: #374151; color: white; border-color: #4b5563; }
        .dark trix-toolbar { background-color: #f3f4f6; border-radius: 0.5rem; margin-bottom: 0.5rem; }
        trix-editor { min-height: 300px; }
        trix-editor ul, trix-editor ol { margin-left: 1.5rem; margin-bottom: 1rem; }
        trix-editor ul { list-style-type: disc; }
        trix-editor ol { list-style-type: decimal; }
        trix-editor blockquote { border-left: 4px solid #ccc; padding-left: 1rem; font-style: italic; }
    </style>
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    <div class="pb-6" x-data="{
        title: @js(old('title', $news->title)),
        slug: @js(old('slug', $news->slug)),
        generateSlug() {
            this.slug = this.title.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-').trim();
        }
    }">
        <div class="mb-8 pt-2">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Edit Berita</h1>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
            {{-- Perhatikan parameter route menggunakan $news --}}
            <form action="{{ route('dashboard.berita.update', $news) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="p-6 space-y-6">
                    {{-- Judul --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Judul Berita <span class="text-red-500">*</span></label>
                        <input type="text" name="title" x-model="title" @input.debounce.500ms="generateSlug()" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                    </div>

                    {{-- Slug --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Slug URL <span class="text-red-500">*</span></label>
                        <input type="text" name="slug" x-model="slug" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white font-mono" required>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Kategori --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Kategori <span class="text-red-500">*</span></label>
                            <select name="news_category_id" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" @selected(old('news_category_id', $news->news_category_id) == $category->id)>{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Link Berita --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Link Berita (Opsional)</label>
                            <input type="url" name="link_berita" value="{{ old('link_berita', $news->link_berita) }}" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                    </div>

                    {{-- Body --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Konten <span class="text-red-500">*</span></label>
                        <input id="body" type="hidden" name="body" value="{{ old('body', $news->body) }}">
                        <trix-editor input="body" class="trix-content bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white min-h-[300px]"></trix-editor>
                    </div>

                    {{-- Gambar --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Gambar Unggulan</label>
                        @if($news->featured_image)
                            <div class="mb-2">
                                <img src="{{ Storage::url($news->featured_image) }}" class="h-32 rounded-lg border border-gray-300">
                            </div>
                        @endif
                        <input type="file" name="featured_image" accept="image/*" class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 dark:bg-gray-700">
                    </div>

                    {{-- Excerpt --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Ringkasan (Opsional)</label>
                        <textarea name="excerpt" rows="3" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white">{{ old('excerpt', $news->excerpt) }}</textarea>
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="dashboard-form-actions flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50">
                    <button type="submit" name="action" value="draft" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100">Simpan Draft</button>
                    <button type="submit" name="action" value="publish" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700">Perbarui & Publikasikan</button>
                </div>
            </form>
        </div>
    </div>
</x-dashboard-layout>
