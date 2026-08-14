<?php

namespace App\Http\Controllers;

use App\Models\Broadcast;
use App\Models\Comment;
use App\Models\News;
use App\Models\Post;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'body' => 'required|string|max:1000',
            'rating' => 'nullable|integer|min:1|max:5',
            'commentable_type' => 'required|string|in:news,post,broadcast',
            'commentable_id' => 'required|integer',
        ]);

        // Honeypot anti-spam check
        if ($request->filled('website')) {
            return back()->withErrors(['error' => 'Terindikasi sebagai spam.']);
        }

        // Map friendly names to model classes
        $typeMap = [
            'news' => News::class,
            'post' => Post::class,
            'broadcast' => Broadcast::class,
        ];

        $modelClass = $typeMap[$validated['commentable_type']];

        // Hanya konten yang sudah benar-benar tayang yang boleh menerima komentar.
        if (! $modelClass::query()
            ->publiclyVisible()
            ->whereKey($validated['commentable_id'])
            ->exists()) {
            return back()->withErrors(['error' => 'Konten tidak ditemukan atau sudah dihapus.']);
        }

        // Store the full class name for the polymorphic relation
        $validated['commentable_type'] = $modelClass;
        $validated['status'] = 'pending';

        Comment::create($validated);

        return back()->with('success', 'Komentar Anda berhasil dikirim! Komentar Anda akan muncul setelah disetujui oleh admin.');
    }
}
