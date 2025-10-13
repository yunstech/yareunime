<?php

namespace App\Services;

use App\Models\{Favorite, Movie};
use Illuminate\Support\Facades\Auth;

class FavoriteService
{
    /**
     * Cek apakah film sudah ada di favorit user
     */
    public function isFavorite(Movie $movie): bool
    {
        $user = Auth::user();

        return Favorite::where('user_id', $user->id)
            ->where('movie_id', $movie->id)
            ->exists();
    }

    /**
     * Toggle favorite (add/remove)
     */
    public function toggleFavorite(Movie $movie): string
    {
        $user = Auth::user();

        $favorite = Favorite::where('user_id', $user->id)
            ->where('movie_id', $movie->id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return 'removed';
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'movie_id' => $movie->id,
            ]);
            return 'added';
        }
    }

    /**
     * Ambil semua film favorit user
     */
    public function getUserFavorites()
    {
        $user = Auth::user();

        return $user->favorites()->with('movie')->latest()->get();
    }
}
