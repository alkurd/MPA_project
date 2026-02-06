<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $fillable = ['naam','user_id'];
    public function User(){
        return $this->belongsTo(User::class);
    }

    public function Songs(){
        return $this->belongsToMany(Song::class, 'playlist_songs');
    }
}
