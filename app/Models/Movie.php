<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'title', 'description', 'slug', 'poster', 'rating', 'background', 'type', 'rating'
    ];

    public function playlists()
    {
        return $this->belongsToMany(Playlist::class, 'playlist_movie')
            ->withTimestamps();
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'target');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function watchProgresses()
    {
        return $this->hasMany(WatchProgress::class);
    }

    public function seasons()
    {
        return $this->hasMany(Season::class);
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'genre_movies', 'movie_id', 'genre_id');
    }
}
