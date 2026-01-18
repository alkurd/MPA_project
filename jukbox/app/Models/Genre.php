<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $fillable= ['naam'];

    public function songs(){
        return $this->hasMany(Song::class);
    }
}
