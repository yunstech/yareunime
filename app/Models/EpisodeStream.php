<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EpisodeStream extends Model
{
    protected $fillable = ['episode_id', 'quality', 'server_name', 'embed_html', 'data_content'];

    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }
}

