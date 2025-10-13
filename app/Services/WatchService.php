<?php

namespace App\Services;

use App\Models\{WatchProgress, Episode};
use Illuminate\Support\Facades\Auth;

class WatchService
{
    /**
     * Simpan atau update progress menonton.
     */
    public function saveProgress(Episode $episode, float $progressTime): void
    {
        $user = Auth::user();

        WatchProgress::updateOrCreate(
            [
                'user_id' => $user->id,
                'episode_id' => $episode->id,
            ],
            [
                'progress_time' => $progressTime,
                'completed' => false,
            ]
        );
    }

    /**
     * Tandai episode selesai.
     */
    public function markCompleted(Episode $episode): void
    {
        $user = Auth::user();

        WatchProgress::where('user_id', $user->id)
            ->where('episode_id', $episode->id)
            ->update([
                'completed' => true,
                'progress_time' => 0,
            ]);
    }

    /**
     * Ambil daftar "Continue Watching" user.
     */
    public function getContinueWatching()
    {
        $user = Auth::user();

        return WatchProgress::with('episode.season.movie')
            ->where('user_id', $user->id)
            ->where('completed', false)
            ->latest()
            ->take(10)
            ->get();
    }

    /**
     * Ambil riwayat tontonan user.
     */
    public function getWatchHistory()
    {
        $user = Auth::user();

        return WatchProgress::with('episode.season.movie')
            ->where('user_id', $user->id)
            ->where('completed', true)
            ->latest('updated_at')
            ->paginate(20);
    }
}
