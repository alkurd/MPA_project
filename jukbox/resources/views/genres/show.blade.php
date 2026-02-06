@extends('layouts.app1')
@section('title', $genre->naam)

@section('content')
<h2 class="page-title">Liedjes in genre: {{ $genre->naam }}</h2>
@if(session('success'))
    <div class="alert-success">
        {{ session('success') }}
    </div>
@endif
@if(session('info'))
    <div class="alert-info">
        {{ session('info') }}
    </div>
@endif

<div class="song-card">
    <table class="song-table">
        <thead>
        <tr>
            <th>Naam</th>
            <th>Artiest</th>
            <th>Duur</th>
            <th>Actie</th>
        </tr>
        </thead>
        <tbody>
        @foreach($songs as $song)
            <tr>
                <td>{{ $song->naam }}</td>
                <td>{{ $song->artist }}</td>
                {{-- <td>{{ floor($song->duration / 60) }}:{{ str_pad($song->duration % 60, 2, '0', STR_PAD_LEFT) }}</td> --}}
                <td >
                        {{ gmdate('i:s', $song['duration']) }}
                    </td>
                <td>
                    @guest
                    <form action="{{ route('playlist.add', $song->id) }}" method="POST">
                    @csrf
                        <button type="submit" >
                            + Toevoegen
                        </button>
                    </form>
                    @endguest
                    
                    @auth
                    <button type="button" class="btn-add-song" data-song-id="{{ $song->id }}" onclick="openAddModal({{ $song->id }})">
                        + Toevoegen
                    </button>
                    @endauth
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <a href="{{ route('genres') }}" class="back-link">← Terug naar genres</a>
</div>

@auth
<!-- Modal voor toevoegen aan playlist -->
<div id="addToPlaylistModal" class="modal">
    <div class="modal-content">
        <span class="close-btn" onclick="closeAddModal()">&times;</span>
        <h3>Nummer toevoegen aan playlist</h3>
        
        <div class="playlist-options">
            @if($hasSessionPlaylist)
            <div class="option-section">
                <h4>Tijdelijke playlist</h4>
                <form action="" method="POST" id="addToSessionForm" data-route="{{ route('playlist.add', 0) }}">
                    @csrf
                    <button type="submit" class="btn-option">Toevoegen aan tijdelijke playlist</button>
                </form>
            </div>
            @endif
            
            @if($playlists->count() > 0)
            <div class="option-section">
                <h4>Bestaande playlist</h4>
                <form action="" method="POST" id="addToExistingForm" data-route="{{ route('playlist.add-to-existing', 0) }}">
                    @csrf
                    <select name="playlist_id" required>
                        <option value="">Kies een playlist...</option>
                        @foreach($playlists as $playlist)
                            <option value="{{ $playlist->id }}">{{ $playlist->naam }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn-option">Toevoegen aan gekozen playlist</button>
                </form>
            </div>
            @endif
            
            <div class="option-section">
                <h4>Nieuwe playlist</h4>
                <form action="" method="POST" id="addToNewForm" data-route="{{ route('playlist.add-to-new', 0) }}">
                    @csrf
                    <input type="text" name="naam" placeholder="Playlist naam" required>
                    <button type="submit" class="btn-option">Maak nieuwe playlist en voeg toe</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endauth

<script>
function openAddModal(songId) {
    const modal = document.getElementById('addToPlaylistModal');
    const sessionForm = document.getElementById('addToSessionForm');
    const existingForm = document.getElementById('addToExistingForm');
    const newForm = document.getElementById('addToNewForm');
    
    // Update song ID in all forms
    // Gebruik regex om alleen de laatste /0 te vervangen (voorkomt bugs bij /10 -> /110)
    if(sessionForm) {
        const route = sessionForm.getAttribute('data-route');
        sessionForm.action = route.replace(/\/0$/, '/' + songId);
    }
    if(existingForm) {
        const route = existingForm.getAttribute('data-route');
        existingForm.action = route.replace(/\/0$/, '/' + songId);
    }
    if(newForm) {
        const route = newForm.getAttribute('data-route');
        newForm.action = route.replace(/\/0$/, '/' + songId);
    }
    
    modal.style.display = "block";
}

function closeAddModal() {
    const modal = document.getElementById('addToPlaylistModal');
    modal.style.display = "none";
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('addToPlaylistModal');
    if (event.target == modal) {
        modal.style.display = "none";
    }
});
</script>

<style>
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0,0,0,0.4);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
    max-width: 500px;
    border-radius: 8px;
}

.close-btn {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close-btn:hover,
.close-btn:focus {
    color: #000;
}

.playlist-options {
    margin-top: 20px;
}

.option-section {
    margin-bottom: 25px;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.option-section:last-child {
    border-bottom: none;
}

.option-section h4 {
    margin-bottom: 10px;
    color: #333;
}

.option-section select,
.option-section input[type="text"] {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
}

.btn-option {
    background-color: #4CAF50;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    width: 100%;
}

.btn-option:hover {
    background-color: #45a049;
}
</style>

@endsection


