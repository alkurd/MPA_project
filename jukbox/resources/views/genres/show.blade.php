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
                    <form action="{{ route('playlist.add', $song->id) }}" method="POST">
                        @csrf
                        <button type="submit" >
                            + Toevoegen
                        </button>
                        @auth
                            <section>
                                <option></option>
                            </section>
                        @endauth
                    </form>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <a href="{{ route('genres') }}" class="back-link">← Terug naar genres</a>
</div>
@endsection


