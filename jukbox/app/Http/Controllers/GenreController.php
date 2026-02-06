<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Genre;
use App\Models\Song;
use App\Models\Playlist;
use Illuminate\Support\Facades\Auth;

class GenreController extends Controller
{
    public function index(){
        $genres = Genre::all();
        return view('genres.index',compact('genres'));
    }
    public function show($id){
        $genre = Genre::with('songs')->findOrFail($id);
        $songs = $genre->songs;
        
        $playlists = [];
        $hasSessionPlaylist = false;
        
        if(auth()->check()){
            // Gebruik relationship via User model (let op: method heet Playlist() met hoofdletter)
            $playlists = auth()->user()->Playlist;
            $sessionPlaylist = session()->get('playlist', []);
            $hasSessionPlaylist = count($sessionPlaylist) > 0;
        }
        
        return view('genres.show',compact('genre','songs', 'playlists', 'hasSessionPlaylist'));
    }
}
