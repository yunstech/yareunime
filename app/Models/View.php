<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    protected $fillable = ['user_id', 'movie_id', 'ip_address'];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
