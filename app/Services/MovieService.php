<?php

namespace App\Services;

use App\Models\{Movie, Season, Episode, Favorite, Playlist};
use Illuminate\Support\Facades\Auth;
use App\Services\FavoriteService;

class MovieService
{
    protected $favoriteService;

    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }
    /**
     * Ambil data untuk halaman Home
     */
    public function getHomeData(): array
    {
        $user = Auth::user();

        return [
            'trendingMovie' => Movie::where('is_trending', true)
                ->inRandomOrder()
                ->first(),

            'continueWatching' => $user
                ? $user->watchProgress()
                    ->with('movie')
                    ->latest()
                    ->take(10)
                    ->get()
                : collect(),

            'topWeekly' => Movie::orderByDesc('rating')
                ->take(10)
                ->get(),

            'popularPlaylists' => Playlist::withCount('movies')
                ->orderByDesc('movies_count')
                ->take(5)
                ->get(),
        ];
    }

    /**
     * Ambil detail film
     */
    public function getMovieDetail(Movie $movie): array
    {
        $user = Auth::user();

        $isFavorite = Auth::check() ? $this->favoriteService->isFavorite($movie) : false;

        return [
            'movie' => $movie->load(['genres', 'seasons']),
            'isFavorite' => $isFavorite,
            'comments' => $movie->comments()
                ->with('user')
                ->latest()
                ->get(),
            'userPlaylists' => $user
                ? $user->playlists()->withCount('movies')->get()
                : collect(),
        ];
    }

    /**
     * Ambil daftar episode per season
     */
    public function getSeasonDetail(Season $season): array
    {
        return [
            'season' => $season->load(['movie', 'episodes']),
        ];
    }

    /**
     * Ambil data episode (untuk halaman watch)
     */
    public function getEpisodeDetail(Episode $episode): array
    {
        return [
            'episode' => $episode->load(['season.movie']),
        ];
    }

    /**
     * Simpan progress menonton (continue watching)
     */
    public function saveWatchProgress(Episode $episode, float $progress): void
    {
        $user = Auth::user();
        if (!$user) return;

        $user->watchProgress()->updateOrCreate(
            ['episode_id' => $episode->id],
            ['progress_time' => $progress, 'completed' => false]
        );
    }

    /**
     * Tandai episode sebagai selesai
     */
    public function markEpisodeWatched(Episode $episode): void
    {
        $user = Auth::user();
        if (!$user) return;

        $user->watchProgress()->updateOrCreate(
            ['episode_id' => $episode->id],
            ['completed' => true, 'progress_time' => 0]
        );
    }

    /**
     * Ambil daftar top movies berdasarkan filter
     */
    public function getTopMovies(string $filter): array
    {
        $query = Movie::query();

        switch ($filter) {
            case 'daily':
                $query->whereDate('created_at', today());
                break;
            case 'weekly':
                $query->whereBetween('created_at', [now()->subWeek(), now()]);
                break;
            case 'monthly':
                $query->whereBetween('created_at', [now()->subMonth(), now()]);
                break;
            case 'alltime':
            default:
                // no filter
                break;
        }

        return [
            'movies' => $query->orderByDesc('rating')->take(20)->get(),
            'filter' => $filter,
        ];
    }
}
