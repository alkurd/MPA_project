<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Song;


class PlaylistController extends Controller
{
    public function add($id){

        $song = Song::findOrFail($id);

        $playlist = session()->get('playlist', []);

        foreach($playlist as $item){
            if($item['id'] == $song['id']){
                return back()->with('info', 'dit nummer bestaat al');
            }
        }
        $playlist[$song->id] = [
        'id'       => $song->id,
        'naam'     => $song->naam,
        'artist'   => $song->artist,
        'duration' => $song->duration,
    ];
        session()->put('playlist', $playlist);
        return redirect()->back()->with('success', 'Song is toegevoegd aan de playlist');
    }

    public function index(){
        $playlist =session()->get('playlist',[]);

        // $tijd = gmdate("i:s");
        return view(' playlist.index',compact('playlist'));
    }

    public function delete($id){
        $playlist =session()->get('playlist',[]);
        if(isset($playlist[$id])){
            unset($playlist[$id]);
            // $playlist = array_values($playlist);
            session()->put('playlist',$playlist);
        }
        return redirect()
        ->route('playlist.index')
        ->with('danger','Nummer is verwijdert');

    }
}
