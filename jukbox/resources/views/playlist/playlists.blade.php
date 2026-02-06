@extends('layouts.app1')
@section('title', 'Play lists')
@section('content')
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
<div class="song-card">
    <table class="song-table">
        <thead>
        <tr>
            <th>Naam</th>
        </tr>
        </thead>
        <tbody>
        @foreach($playlists as $playlist)
            <tr>
                <td>{{ $playlist->naam }}</td>

        @endforeach
        </tbody>
    </table>

    <a href="{{ route('genres') }}" class="back-link">← Terug naar genres</a>
</div>
@endsection
