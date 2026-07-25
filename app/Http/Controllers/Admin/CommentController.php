<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index(Request $request)
    {
        $query = Comment::with(['post'])->latest();

        if ($request->filled('status')) {
            if ($request->status === 'pending') {
                $query->where('is_approved', false);
            } elseif ($request->status === 'approved') {
                $query->where('is_approved', true);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('body', 'like', "%{$search}%");
            });
        }

        $comments = $query->paginate(15)->withQueryString();
        $pendingCount = Comment::where('is_approved', false)->count();
        $approvedCount = Comment::where('is_approved', true)->count();

        return view('admin.comments.index', compact('comments', 'pendingCount', 'approvedCount'));
    }

    public function approve(Comment $comment)
    {
        $comment->update(['is_approved' => true]);

        return redirect()->back()->with('success', 'Komentar berhasil disetujui.');
    }

    public function reject(Comment $comment)
    {
        $comment->update(['is_approved' => false]);

        return redirect()->back()->with('success', 'Komentar berhasil ditolak.');
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();

        return redirect()->back()->with('success', 'Komentar berhasil dihapus.');
    }
}
