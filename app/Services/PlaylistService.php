<?php

namespace App\Services;

use App\Models\{Playlist, Movie, PlaylistMovie};
use Illuminate\Support\Facades\Auth;

class PlaylistService
{
    public function __construct(private NotificationService $notificationService) {}

    /**
     * Ambil semua playlist milik user
     */
    public function getUserPlaylists(): array
    {
        $user = Auth::user();

        return [
            'playlists' => Playlist::where('user_id', $user->id)
                ->withCount('movies')
                ->latest()
                ->get(),
        ];
    }

    /**
     * Ambil detail playlist beserta film dan komentar
     */
    public function getPlaylistDetail(Playlist $playlist): array
    {
        $playlist->load(['movies', 'user']);

        return [
            'playlist' => $playlist,
            'comments' => $playlist->comments()
                ->with('user')
                ->latest()
                ->get(),
        ];
    }

    /**
     * Buat playlist baru
     */
    public function createPlaylist(array $data): Playlist
    {
        $user = Auth::user();

        return Playlist::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'description' => $data['description'] ?? '',
            'is_public' => $data['is_public'] ?? false,
        ]);
    }

    /**
     * Tambahkan film ke playlist
     */
    public function addMovieToPlaylist(array $data): void
    {
        PlaylistMovie::firstOrCreate([
            'playlist_id' => $data['playlist_id'],
            'movie_id' => $data['movie_id'],
        ]);

        $playlist = Playlist::find($data['playlist_id']);
        $user = Auth::user();

        // Kirim notifikasi ke pemilik playlist
        if ($playlist->user_id !== $user->id) {
            $this->notificationService->send(
                $playlist->user_id,
                'Film Baru Ditambahkan 🎬',
                "{$user->name} menambahkan film ke playlist kamu: {$playlist->name}",
                route('playlist.show', $playlist->id)
            );
        }
    }

    /**
     * Hapus film dari playlist
     */
    public function removeMovieFromPlaylist(int $playlistId, int $movieId): void
    {
        PlaylistMovie::where('playlist_id', $playlistId)
            ->where('movie_id', $movieId)
            ->delete();
    }

    /**
     * Hapus playlist milik user
     */
    public function deletePlaylist(Playlist $playlist): void
    {
        $user = Auth::user();

        if ($playlist->user_id === $user->id) {
            $playlist->movies()->detach();
            $playlist->delete();
        }
    }
}
