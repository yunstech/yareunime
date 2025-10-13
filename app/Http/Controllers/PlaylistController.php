<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Services\PlaylistService;
use Illuminate\Http\Request;

class PlaylistController extends Controller
{
    protected $playlistService;

    public function __construct(PlaylistService $playlistService)
    {
        $this->playlistService = $playlistService;
    }

    public function index()
    {
        $data = $this->playlistService->getUserPlaylists();
        return view('playlist.index', $data);
    }

    public function show(Playlist $playlist)
    {
        $data = $this->playlistService->getPlaylistDetail($playlist);
        return view('playlist.show', $data);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'is_public' => 'nullable|boolean',
        ]);

        $this->playlistService->createPlaylist($validated);
        return redirect()->route('playlist.index')->with('success', 'Playlist berhasil dibuat!');
    }

    public function addMovie(Request $request)
    {
        $validated = $request->validate([
            'playlist_id' => 'required|exists:playlists,id',
            'movie_id' => 'required|exists:movies,id',
        ]);

        $this->playlistService->addMovieToPlaylist($validated);
        return back()->with('success', 'Film berhasil ditambahkan ke playlist!');
    }

    public function removeMovie($playlistId, $movieId)
    {
        $this->playlistService->removeMovieFromPlaylist($playlistId, $movieId);
        return back()->with('success', 'Film berhasil dihapus dari playlist!');
    }

    public function destroy(Playlist $playlist)
    {
        $this->playlistService->deletePlaylist($playlist);
        return redirect()->route('playlist.index')->with('success', 'Playlist dihapus.');
    }
}
