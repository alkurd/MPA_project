@extends('layouts.app1');
@section('title', 'Play list');
@section('content')
@if (session('danger')){
    <div class="alert-danger">
        {{ session('danger') }}
    </div>
}
@endif
@if ()

@else
@foreach ($playlist as $item){
    
}
@endif

@endforeach
@endsection
