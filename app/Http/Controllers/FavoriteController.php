<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Services\FavoriteService;
use Illuminate\Http\Request;

class FavoriteController extends Controller
{
    protected $favoriteService;

    public function __construct(FavoriteService $favoriteService)
    {
        $this->favoriteService = $favoriteService;
    }

    /**
     * Tambah / hapus film dari favorit
     */
    public function toggle(Movie $movie)
    {
        $status = $this->favoriteService->toggleFavorite($movie);

        return back()->with(
            $status === 'added' ? 'success' : 'info',
            $status === 'added'
                ? 'Film ditambahkan ke favorit!'
                : 'Film dihapus dari favorit.'
        );
    }

    /**
     * Halaman daftar favorit user
     */
    public function index()
    {
        $favorites = $this->favoriteService->getUserFavorites();

        return view('profile.favorites', compact('favorites'));
    }
}
