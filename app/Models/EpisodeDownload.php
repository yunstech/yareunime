<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EpisodeDownload extends Model
{
    protected $fillable = ['episode_id', 'label', 'url'];

    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }
}
