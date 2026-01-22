@extends('layouts.app1')
@section('title', 'Play list')
@section('content')
<h2 class="page-title">Playlist</h2>
@if (session('danger'))
    <div class="alert-danger">
        {{ session('danger') }}
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
    <form method="POST" action="add" class="playlist-maken">
        @csrf
        <button type="submit">Playlist opslaan </button>
    </form>
@endauth
<a class="back-link" href="{{ route('genres') }}">{{ 'all genres' }}</a>


@endsection




