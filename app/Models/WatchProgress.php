<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatchProgress extends Model
{
    protected $fillable = ['user_id', 'episode_id', 'progress_time', 'completed'];

    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
