<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Services\CommentService;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    protected $commentService;

    public function __construct(CommentService $commentService)
    {
        $this->commentService = $commentService;
    }

    /**
     * Simpan komentar baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'content'  => 'required|string|max:500',
        ]);

        $comment = Comment::create([
            'movie_id' => $validated['movie_id'],
            'user_id'  => auth()->id(), // pastikan user login
            'content'  => $validated['content'],
        ]);

        return redirect()->back()->with('success', 'Comment added successfully!');
    }

    /**
     * Hapus komentar
     */
    public function destroy(Comment $comment)
    {
        $this->commentService->deleteComment($comment);
        return back()->with('info', 'Komentar dihapus.');
    }
}
