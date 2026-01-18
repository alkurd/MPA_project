@extends('layouts.app1')
@section('title', 'Alle Genres')

@section('content')
<h2 class="page-title">Alle Genres</h2>

<div class="genre-list">
    <ul>
        @foreach($genres as $genre)
            <li>
                <a href="{{ route('genres.show', $genre->id) }}">
                    {{ $genre->naam }}
                </a>
            </li>
        @endforeach
    </ul>
</div>
@endsection
