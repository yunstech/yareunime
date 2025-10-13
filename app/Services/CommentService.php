<?php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Support\Facades\Auth;

class CommentService
{

    public function __construct(private NotificationService $notificationService) {}


    public function getComments($targetType, $targetId)
    {
        return Comment::where('target_type', $targetType)
            ->where('target_id', $targetId)
            ->with('user')
            ->latest()
            ->get();
    }

    public function storeComment(array $data): Comment
    {
        $user = Auth::user();

        $comment = Comment::create([
            'user_id' => $user->id,
            'target_type' => $data['target_type'],
            'target_id' => $data['target_id'],
            'content' => $data['content'],
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        // Kirim notifikasi ke pemilik target
        $target = $comment->target;
        if (method_exists($target, 'user') && $target->user && $target->user->id !== $user->id) {
            $this->notificationService->send(
                $target->user->id,
                'Komentar Baru 💬',
                "{$user->name} mengomentari {$target->title}",
                route('movie.show', $target->id)
            );
        }

        return $comment;
    }

    public function deleteComment(Comment $comment): void
    {
        $user = Auth::user();
        if ($comment->user_id === $user->id) {
            $comment->delete();
        }
    }
}
