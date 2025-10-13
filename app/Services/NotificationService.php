<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class NotificationService
{
    /**
     * Kirim notifikasi ke user tertentu
     */
    public function send(int $userId, string $title, string $message, ?string $link = null): void
    {
        Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'link' => $link,
        ]);
    }

    /**
     * Ambil semua notifikasi user
     */
    public function getUserNotifications(): array
    {
        $user = Auth::user();

        return [
            'notifications' => Notification::where('user_id', $user->id)
                ->latest()
                ->take(30)
                ->get(),
        ];
    }

    /**
     * Tandai notifikasi sebagai dibaca
     */
    public function markAsRead(int $id): void
    {
        $notif = Notification::find($id);
        if ($notif && $notif->user_id === Auth::id()) {
            $notif->update(['is_read' => true]);
        }
    }

    /**
     * Tandai semua notifikasi sebagai dibaca
     */
    public function markAllAsRead(): void
    {
        Notification::where('user_id', Auth::id())
            ->update(['is_read' => true]);
    }
}
