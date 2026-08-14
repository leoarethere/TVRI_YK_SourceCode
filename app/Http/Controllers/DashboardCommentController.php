<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Support\LikeSearch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardCommentController extends Controller
{
    public function index(Request $request): View
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
        ]);

        $query = Comment::with('commentable');

        if ($request->filled('search')) {
            $search = $request->string('search')->toString();
            $query->where(function ($q) use ($search): void {
                LikeSearch::contains($q, 'name', $search);
                LikeSearch::contains($q, 'email', $search, 'or');
                LikeSearch::contains($q, 'body', $search, 'or');
            });
        }

        if ($request->filled('status') && in_array($request->status, ['pending', 'approved', 'rejected'])) {
            $query->where('status', $request->status);
        }

        $comments = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'all' => Comment::count(),
            'pending' => Comment::pending()->count(),
            'approved' => Comment::approved()->count(),
            'rejected' => Comment::rejected()->count(),
        ];

        return view('backend.comments.index', compact('comments', 'counts'));
    }

    public function approve(Comment $comment): RedirectResponse
    {
        $comment->update(['status' => 'approved']);

        return back()->with('success', 'Komentar dari "'.$comment->name.'" berhasil disetujui.');
    }

    public function reject(Comment $comment): RedirectResponse
    {
        $comment->update(['status' => 'rejected']);

        return back()->with('success', 'Komentar dari "'.$comment->name.'" berhasil ditolak.');
    }

    public function destroy(Comment $comment): RedirectResponse
    {
        $comment->delete();

        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}
