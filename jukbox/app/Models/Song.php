<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    protected $fillable = ['genre_id','naam', 'artist', 'duration'];
    public function genre(){
        return $this->belongsTo(Genre::class);
    }
}
