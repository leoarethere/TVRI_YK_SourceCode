<x-dashboard-layout>
    <x-slot:title>Edit Media Sosial</x-slot:title>

    <div class="pb-6">
        <div class="flex flex-col md:flex-row md:justify-between md:items-start gap-4 mb-8 pt-2">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">
                    Edit Media Sosial
                </h1>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Perbarui tautan media sosial. <span class="text-red-500 font-semibold">Wajib gunakan awalan https://</span>
                </p>
            </div>
        </div>

        @if ($errors->any())
            <div class="mb-6 p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-200 dark:border-gray-700 overflow-hidden">
            {{-- Perubahan di sini: route update menggunakan ID --}}
            <form action="{{ route('dashboard.social-media.update', $socialMedia->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="p-6 space-y-6">
                    
                    {{-- Grid Input Sosial Media --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Instagram --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Instagram URL</label>
                            <input type="url" name="instagram" value="{{ old('instagram', $socialMedia->instagram) }}" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                placeholder="https://instagram.com/username">
                        </div>

                        {{-- Facebook --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Facebook URL</label>
                            <input type="url" name="facebook" value="{{ old('facebook', $socialMedia->facebook) }}" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                placeholder="https://facebook.com/username">
                        </div>

                        {{-- Twitter / X --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">X (Twitter) URL</label>
                            <input type="url" name="twitter" value="{{ old('twitter', $socialMedia->twitter) }}" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                placeholder="https://x.com/username">
                        </div>

                        {{-- YouTube --}}
                        <div>
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">YouTube URL</label>
                            <input type="url" name="youtube" value="{{ old('youtube', $socialMedia->youtube) }}" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                placeholder="https://youtube.com/@channel">
                        </div>

                        {{-- TikTok --}}
                        <div class="md:col-span-2">
                            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">TikTok URL</label>
                            <input type="url" name="tiktok" value="{{ old('tiktok', $socialMedia->tiktok) }}" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:text-white" 
                                placeholder="https://tiktok.com/@username">
                        </div>

                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <div class="dashboard-form-actions flex items-center justify-between gap-3 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700/50 rounded-b-lg">
                    <a href="{{ route('dashboard.social-media.index') }}" class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-100 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800 transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-dashboard-layout>
