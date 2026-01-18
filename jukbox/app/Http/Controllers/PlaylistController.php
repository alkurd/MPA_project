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
        $playlist[] = [
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
        $totaal = 0;
        foreach($playlist as $song){
            $totaal += $song['duration'];
        }
        $tijd = gmdate("i:s");
        // $minuten  = floor($totaal / 60);
        // $seconden = $totaal % 60;
        // $tijd = $minuten .':'.str_pad($seconden,2,'0',STR_PAD_LEFT);
        return view(' playlist.index',compact('playlist', 'tijd'));
    }
    public function delete($index){
        $playlist =session()->get('playlist',[]);
        if(isset($playlist[$index])){
            unset($playlist[$index]);
            // $playlist = array_values($playlist);
            session()->put('playlist',$playlist);
        }
        return redirect()
        ->route('playlist.index')
        ->with('info','nummer is verwijdert');

    }
}
