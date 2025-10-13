<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Services\WatchService;
use Illuminate\Http\Request;

class WatchController extends Controller
{
    protected $watchService;

    public function __construct(WatchService $watchService)
    {
        $this->watchService = $watchService;
    }

    public function saveProgress(Request $request, Episode $episode)
    {
        $progress = $request->input('progress', 0);
        $this->watchService->saveProgress($episode, $progress);
        return response()->json(['status' => 'ok']);
    }

    public function markCompleted(Episode $episode)
    {
        $this->watchService->markCompleted($episode);
        return back()->with('success', 'Episode ditandai selesai.');
    }

    public function history()
    {
        $data = [
            'histories' => $this->watchService->getWatchHistory(),
        ];

        return view('profile.history', $data);
    }
}
