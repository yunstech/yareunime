<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    protected $fillable = ['season_id', 'episode_number', 'episode_page_url', 'title', 'thumbnail', 'duration'];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function streams()
    {
        return $this->hasMany(EpisodeStream::class);
    }

    public function downloads()
    {
        return $this->hasMany(EpisodeDownload::class);
    }

}
