<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function index()
    {
        $data = $this->notificationService->getUserNotifications();
        return view('profile.notifications', $data);
    }

    public function read($id)
    {
        $this->notificationService->markAsRead($id);
        return back();
    }

    public function readAll()
    {
        $this->notificationService->markAllAsRead();
        return back()->with('info', 'Semua notifikasi ditandai sebagai dibaca.');
    }
}
