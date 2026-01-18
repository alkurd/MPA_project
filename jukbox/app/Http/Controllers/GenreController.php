<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genre;
use App\Models\Song;

class GenreController extends Controller
{
    public function index(){
        $genres = Genre::all();
        return view('genres.index',compact('genres'));
    }
    public function show($id){
        $genre = Genre::with('songs')->findOrFail($id);
        $songs = $genre->songs;
        return view('genres.show',compact('genre','songs'));
    }
}
