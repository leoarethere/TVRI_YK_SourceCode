@props(['model'])

<div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Komentar & Rating</h3>

    @php
        $totalComments = $model->comments()->approved()->count();
        $ratingStats = $model->comments()->approved()->whereNotNull('rating')
            // comments() diurutkan dengan latest(). Aggregate MySQL strict tidak
            // boleh membawa ORDER BY created_at tanpa GROUP BY.
            ->reorder()
            ->selectRaw('AVG(rating) as avg_rating, COUNT(*) as total_ratings')
            ->first();
        $averageRating = $ratingStats->avg_rating;
        $totalRatings = $ratingStats->total_ratings;
        $comments = $model->comments()->approved()->paginate(10);
    @endphp

    {{-- Notifikasi Sukses --}}
    @if(session('success'))
        <div class="p-4 mb-6 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400" role="alert">
            <span class="font-medium">Sukses!</span> {{ session('success') }}
        </div>
    @endif

    {{-- Notifikasi Error --}}
    @if($errors->any())
        <div class="p-4 mb-6 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400" role="alert">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Form Tambah Komentar --}}
    <div class="bg-gray-50 dark:bg-gray-800/50 p-6 rounded-xl mb-8">
        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tinggalkan Komentar</h4>
        <form action="{{ route('komentar.store') }}" method="POST">
            @csrf
            <input type="hidden" name="commentable_type" value="{{ strtolower(class_basename($model)) }}">
            <input type="hidden" name="commentable_id" value="{{ $model->id }}">

            {{-- Honeypot: tidak terlihat oleh manusia, tapi akan diisi bot --}}
            <div style="display: none;" aria-hidden="true">
                <input type="text" name="website" tabindex="-1" autocomplete="off">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nama Anda *</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required autocomplete="name" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="John Doe">
                </div>
                <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Email Anda * <span class="text-xs text-gray-500 font-normal">(Tidak akan dipublikasikan)</span></label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required autocomplete="email" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="john@example.com">
                </div>
            </div>

            <div class="mb-4">
                <label for="rating" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Rating (Opsional)</label>
                <select id="rating" name="rating" class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white">
                    <option value="">-- Pilih Rating --</option>
                    <option value="5" @selected(old('rating') == '5')>⭐⭐⭐⭐⭐ (5/5) Sangat Baik</option>
                    <option value="4" @selected(old('rating') == '4')>⭐⭐⭐⭐ (4/5) Baik</option>
                    <option value="3" @selected(old('rating') == '3')>⭐⭐⭐ (3/5) Cukup</option>
                    <option value="2" @selected(old('rating') == '2')>⭐⭐ (2/5) Kurang</option>
                    <option value="1" @selected(old('rating') == '1')>⭐ (1/5) Sangat Kurang</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="body" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Komentar Anda *</label>
                <textarea id="body" name="body" rows="4" required class="bg-white border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white" placeholder="Tulis komentar Anda di sini...">{{ old('body') }}</textarea>
            </div>

            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                Kirim Komentar
            </button>
        </form>
    </div>

    {{-- Daftar Komentar --}}
    <div class="space-y-6">
        @if($totalComments > 0)
            <div class="flex items-center gap-2 mb-6">
                <span class="text-lg font-bold text-gray-900 dark:text-white">{{ $totalComments }} Komentar</span>
                @if($totalRatings > 0)
                    <span class="text-gray-400">|</span>
                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                        Rating: <span class="text-yellow-400 font-bold">★ {{ number_format($averageRating, 1) }}</span> 
                        <span class="text-xs text-gray-500">({{ $totalRatings }} ulasan)</span>
                    </span>
                @endif
            </div>

            @foreach($comments as $comment)
                <div class="p-4 bg-white rounded-lg border border-gray-100 shadow-sm dark:bg-gray-800 dark:border-gray-700">
                    <div class="flex justify-between items-center mb-2">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-sm">
                                {{ strtoupper(substr($comment->name ?? '?', 0, 1)) }}
                            </div>
                            <div>
                                <span class="font-semibold text-sm text-gray-900 dark:text-white">{{ $comment->name }}</span>
                                <span class="text-xs text-gray-500 block">{{ $comment->created_at ? $comment->created_at->diffForHumans() : '' }}</span>
                            </div>
                        </div>
                        @if($comment->rating)
                            <div class="flex text-yellow-400 text-sm" title="Nilai: {{ $comment->rating }}/5">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= $comment->rating)
                                        ★
                                    @else
                                        <span class="text-gray-300 dark:text-gray-600">★</span>
                                    @endif
                                @endfor
                            </div>
                        @endif
                    </div>
                    <p class="text-gray-700 dark:text-gray-300 text-sm mt-3 whitespace-pre-line">{{ $comment->body }}</p>
                </div>
            @endforeach

            {{-- Pagination --}}
            @if($comments->hasPages())
                <div class="mt-6">
                    {{ $comments->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        @else
            <div class="text-center py-8 text-gray-500 dark:text-gray-400 italic">
                Belum ada komentar. Jadilah yang pertama berkomentar!
            </div>
        @endif
    </div>
</div>
