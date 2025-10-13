<?php

namespace App\Http\Controllers;

use App\Models\{Movie, Season, Episode, View};
use App\Services\MovieService;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    protected $movieService;

    public function __construct(MovieService $movieService)
    {
        $this->movieService = $movieService;
    }

    public function index()
    {
        $featured = Movie::inRandomOrder()->first();
        $ongoing = Movie::where('type', 'anime')->latest()->take(10)->get();
        $weeklyTop = Movie::inRandomOrder()->take(10)->get();
        $topAllTime = Movie::orderBy('id', 'desc')->take(12)->get();

        return view('home', compact('featured', 'ongoing', 'weeklyTop', 'topAllTime'));
    }
    public function show($slug)
    {
        $movie = Movie::where('slug', $slug)
            ->with(['genres', 'seasons.episodes'])
            ->firstOrFail();

        return view('movie.show', compact('movie'));
    }

    public function play($slug)
    {
        $movie = \App\Models\Movie::where('slug', $slug)
            ->with('seasons.episodes')
            ->firstOrFail();

        $firstEpisode = $movie->seasons
            ->flatMap(fn($s) => $s->episodes)
            ->sortBy('episode_number')
            ->first();

        if ($firstEpisode) {
            return redirect()->route('episode.show', $firstEpisode->id);
        }

        return redirect()->route('movie.show', $movie->slug)
            ->with('error', 'No episodes available yet.');
    }

    public function season(Season $season)
    {
        $data = $this->movieService->getSeasonDetail($season);
        return view('movie.season', $data);
    }

    public function episode(Episode $episode)
    {
        $data = $this->movieService->getEpisodeDetail($episode);
        return view('movie.episode', $data);
    }

    public function saveProgress(Request $request, Episode $episode)
    {
        $this->movieService->saveWatchProgress($episode, $request->input('progress', 0));
        return response()->json(['status' => 'ok']);
    }

    public function markWatched(Episode $episode)
    {
        $this->movieService->markEpisodeWatched($episode);
        return back()->with('success', 'Episode ditandai sebagai selesai.');
    }

    public function top(Request $request)
    {
        $filter = $request->get('filter', 'weekly');
        $data = $this->movieService->getTopMovies($filter);
        return view('top', $data);
    }
}
