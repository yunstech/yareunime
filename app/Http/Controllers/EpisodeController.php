<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use Illuminate\Http\Request;

class EpisodeController extends Controller
{
    public function show($id)
    {
        $episode = Episode::with(['season.movie', 'streams', 'downloads'])->findOrFail($id);

        $prevEpisode = Episode::where('season_id', $episode->season_id)
            ->where('episode_number', '<', $episode->episode_number)
            ->orderByDesc('episode_number')
            ->first();

        $nextEpisode = Episode::where('season_id', $episode->season_id)
            ->where('episode_number', '>', $episode->episode_number)
            ->orderBy('episode_number')
            ->first();

        return view('movie.episode', compact('episode', 'prevEpisode', 'nextEpisode'));
    }
}
