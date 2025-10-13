<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $fillable = ['user_id', 'name', 'description', 'is_public'];

    public function movies()
    {
        return $this->belongsToMany(Movie::class, 'playlist_movie')
            ->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function comments()
    {
        return $this->morphMany(Comment::class, 'target');
    }
}

