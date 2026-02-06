@extends('layouts.app1')
@section('title', 'Play list')
@section('content')

<h2 class="page-title">Playlist</h2>
@if (session(('info')))
    <div class="alert-info">{{ session(key: 'info') }}</div>

@endif
@if (session('danger'))
    <div class="alert-danger">
        {{ session('danger') }}
    </div>

@endif
@if (session('success'))
    <div class="alert-success">
        {{ session('success') }}
    </div>

@endif

@if (empty($playlist))
<p>Je playlist is leeg</p>
@else
<table>
    <thead>
            <th>Naam</th>
            <th>Artist</th>
            <th>Lengt</th>
            <th></th>
    </thead>
    <tbody>
        @foreach ($playlist as $song)
            <tr>
                <td>{{ $song['naam'] }}</td>
                <td>{{ $song['artist'] }}</td>
                <td>{{  gmdate("i:s" , $song['duration']) }}</td>
                <td>
                    <form method="POST" action="{{ route('playlist.delete', $song['id']) }}">
                        @csrf
                        @method('DELETE')
                        <button class="verwijderen">Verwijderen</button>
                    </form>
                </td>
            </tr>


        @endforeach
    </tbody>
</table>
@endif
@auth
    <button type="button" id="openModalBtn" class="btn-primary">Nieuwe Playlist Maken</button>

    <div id="playlistModal" class="modal">
        <div class="modal-content">
            <span class="close-btn">&times;</span>
            <h3>Playlist Opslaan</h3>

            <form method="POST" action="{{route('save')}}" id="sa" class="playlist-maken">
                @csrf
                <input type="text" name="naam" placeholder="Playlist naam" required>
                <button type="submit">Opslaan</button>
            </form>
        </div>
    </div>
@endauth
<a class="back-link" href="{{ route('genres') }}">{{ 'all genres' }}</a>
<script>
    document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById("playlistModal");
    const btn = document.getElementById("openModalBtn");
    const closeBtn = document.querySelector(".close-btn");

    // Open de modal
    if(btn) {
        btn.onclick = function() {
            modal.style.display = "block";
        }
    }

    // Sluit de modal via het kruisje
    if(closeBtn) {
        closeBtn.onclick = function() {
            modal.style.display = "none";
        }
    }

    // Sluit de modal als je buiten het venster klikt
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
});
</script>

@endsection




