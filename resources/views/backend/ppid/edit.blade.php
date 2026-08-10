<x-dashboard-layout>
    <x-slot:title>Edit Dokumen PPID</x-slot:title>

    {{-- Style Trix --}}
    <link rel="stylesheet" type="text/css" href="https://unpkg.com/trix@2.0.8/dist/trix.css">
    <style>
        trix-toolbar [data-trix-button-group="file-tools"] { display: none; }
        .dark trix-editor { background-color: #374151; color: white; border-color: #4b5563; }
        .dark trix-toolbar { background-color: #f3f4f6; border-radius: 0.5rem; margin-bottom: 0.5rem; }
        trix-editor { min-height: 250px; }
        trix-editor ul { list-style-type: disc !important; padding-left: 1.5rem !important; margin-bottom: 1rem !important; }
        trix-editor ol { list-style-type: decimal !important; padding-left: 1.5rem !important; margin-bottom: 1rem !important; }
        trix-editor li { margin-bottom: 0.5rem; }
        trix-editor blockquote { border-left: 4px solid #e5e7eb; padding-left: 1rem; margin-left: 0; font-style: italic; color: #4b5563; }
        .dark trix-editor blockquote { border-color: #4b5563; color: #9ca3af; }
    </style>
    <script type="text/javascript" src="https://unpkg.com/trix@2.0.8/dist/trix.umd.min.js"></script>

    <div class="pb-6">
        {{-- HEADER HALAMAN --}}
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-8 pt-2">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                    Edit Dokumen
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Perbarui informasi dokumen PPID.
                </p>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
            <form action="{{ route('dashboard.ppid.update', $ppid->id) }}" method="POST" enctype="multipart/form-data">
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
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Judul Informasi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" value="{{ old('title', $ppid->title) }}" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                        @error('title') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Input Link --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Link Sumber Dokumen <span class="text-red-500">*</span>
                            </label>
                            
                            @if($ppid->source_link)
                            <div class="mb-2 text-xs text-blue-600 dark:text-blue-400">
                                Link saat ini: <a href="{{ $ppid->source_link }}" target="_blank" class="underline hover:text-blue-800 dark:hover:text-blue-300">Buka Link</a>
                            </div>
                            @endif

                            <input type="url" name="source_link" value="{{ old('source_link', $ppid->source_link) }}" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" required>
                            @error('source_link') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- Cover Image --}}
                        <div x-data="{ 
                            preview: '{{ $ppid->cover_image ? Storage::url($ppid->cover_image) : null }}',
                            handleFile(event) {
                                const file = event.target.files[0];
                                if (!file) return;
                                if (file.size > 5 * 1024 * 1024) {
                                    alert('Ukuran file terlalu besar. Maksimal 5MB.');
                                    event.target.value = '';
                                    return;
                                }
                                this.preview = URL.createObjectURL(file);
                            }
                        }">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                                Cover Gambar (Opsional)
                            </label>
                            
                            {{-- Preview Image --}}
                            <div class="mb-3" x-show="preview">
                                <div class="relative w-32 h-32 rounded-lg overflow-hidden border border-gray-300 dark:border-gray-600">
                                    <img :src="preview" class="w-full h-full object-cover">
                                    <button type="button" @click="preview = null; document.getElementById('cover_image').value = ''" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            </div>

                            <input type="file" name="cover_image" id="cover_image" @change="handleFile($event)" 
                                class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" 
                                accept="image/png, image/jpeg, image/jpg, image/webp">
                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Biarkan kosong jika tidak ingin mengubah cover.</p>
                            @error('cover_image') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Keterangan (Trix) --}}
                    <div>
                        <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                            Keterangan / Deskripsi <span class="text-red-500">*</span>
                        </label>
                        <input id="description" type="hidden" name="description" value="{{ old('description', $ppid->description) }}">
                        <trix-editor input="description" class="trix-content bg-gray-50 border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white"></trix-editor>
                        @error('description') <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Status Toggle --}}
                    <div class="flex items-center">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', $ppid->is_active) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 dark:peer-focus:ring-blue-800 rounded-full peer dark:bg-gray-700 peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-gray-600 peer-checked:bg-blue-600"></div>
                            <span class="ml-3 text-sm font-medium text-gray-900 dark:text-gray-300">Status Aktif</span>
                        </label>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="dashboard-form-actions flex items-center justify-between gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 rounded-b-lg">
                    <a href="{{ route('dashboard.ppid.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">Batal</a>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 transition-colors">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</x-dashboard-layout>
