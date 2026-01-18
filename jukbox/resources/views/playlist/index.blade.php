@extends('layouts.app1');
@section('title', 'Play list');
@section('content')
@if (session('danger')){
    <div class="alert-danger">
        {{ session('danger') }}
    </div>
}
@endif

@if(session('info'))
    <div class="alert-info">
        {{ session('info') }}
    </div>
@endif

@if (empty($playlist))
<p>Je playlist is leeg</p>
@else
<table>
    <thead>
        <tr>
            <th>Naam</th>
            <th>Artist</th>
            <th>Lengt</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($playlist as $song){
            <tr>
                <td>{{ $song['naam'] }}</td>
                <td>{{ $song['artist'] }}</td>
                <td>{{  $song['artist'] }}</td>
                <td>
                    <form method="POST" action="{{ route('playlist.delete', $song['id']) }}">
                        @csrf
                        @method('DELETE')
                        <button class="text-red-600">Verwijderen</button>
                    </form>
                </td>
            </tr>
        }

        @endforeach
    </tbody>
</table>
@endif
<a href="{{ route('genres.index') }}">{{ 'all genres' }}</a>


@endsection
