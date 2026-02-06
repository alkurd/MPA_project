<?php

namespace App\Http\Controllers;

use App\Models\Playlist;
use App\Models\Song;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
// use App\Models\Playlist;



class PlaylistController extends Controller
{
    public function add($id){

        $song = Song::findOrFail($id);

        $playlist = session()->get('playlist', []);

        if(isset($playlist[$song->id])){
            return back()->with('info','Dit nummer bestaat al');
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
    public function save(Request $request){
        if(!auth()->check()){
            return redirect()->route('login');
        }
        $user = Auth::user();

        $sessionPlaylist =session()->get('playlist',[]);
        if(empty($sessionPlaylist)){
            return back()->with('info', 'er is niks om op te slaan');
        }
        $request->validate([
        'naam' => 'required|string|max:255',
        ]);

        $playlist = Playlist::create([
            'user_id' => auth()->id(),
            'naam' => $request->naam,
        ]);

        foreach($sessionPlaylist as $song){
            $playlist->songs()->attach($song['id']);
        }
        session()->forget('playlist');
        return redirect()->route('playlists');
    }

    public function show(){
        $playlists = Playlist::where('user_id', auth()->id())->get();
        return view('playlist.playlists',compact('playlists'));
    }

    public function addToExistingPlaylist(Request $request, $id){
        $song = Song::findOrFail($id);
        
        // Security: Valideer dat playlist bestaat EN van de ingelogde gebruiker is
        $request->validate([
            'playlist_id' => [
                'required',
                Rule::exists('playlists', 'id')->where('user_id', auth()->id())
            ]
        ]);

        $playlist = Playlist::where('user_id', auth()->id())
            ->findOrFail($request->playlist_id);

        // Check if song already exists in playlist (gebruik relationship direct)
        if($playlist->songs->contains($song->id)){
            return back()->with('info', 'Dit nummer bestaat al in deze playlist');
        }

        $playlist->songs()->attach($song->id);
        return redirect()->back()->with('success', 'Song is toegevoegd aan de playlist');
    }

    public function addToNewPlaylist(Request $request, $id){
        $song = Song::findOrFail($id);
        
        // Valideer playlist naam (optioneel: uniek per gebruiker)
        $request->validate([
            'naam' => [
                'required',
                'string',
                'max:255',
                // Optioneel: uniek per gebruiker
                // Rule::unique('playlists', 'naam')->where('user_id', auth()->id())
            ]
        ]);

        $playlist = Playlist::create([
            'user_id' => auth()->id(),
            'naam' => $request->naam,
        ]);

        $playlist->songs()->attach($song->id);
        return redirect()->back()->with('success', 'Song is toegevoegd aan de nieuwe playlist');
    }
}
