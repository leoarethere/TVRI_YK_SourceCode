<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request)
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
            'news' => \App\Models\News::class,
            'post' => \App\Models\Post::class,
            'broadcast' => \App\Models\Broadcast::class,
        ];

        $modelClass = $typeMap[$validated['commentable_type']];

        // Verify the model exists
        if (!$modelClass::where('id', $validated['commentable_id'])->exists()) {
            return back()->withErrors(['error' => 'Konten tidak ditemukan atau sudah dihapus.']);
        }

        // Store the full class name for the polymorphic relation
        $validated['commentable_type'] = $modelClass;
        $validated['status'] = 'pending';

        Comment::create($validated);

        return back()->with('success', 'Komentar Anda berhasil dikirim! Komentar Anda akan muncul setelah disetujui oleh admin.');
    }
}
