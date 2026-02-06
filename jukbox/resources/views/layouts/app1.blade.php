<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Muziek App')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<header class="site-header">
    <h1 class="h1-header">Muziek Bibliotheek</h1>
    <input type="hidden" name="previous_url" value="{{ url()->current() }}">
@guest
    <div class="log-in" >
        <form method="GET" action="{{ route('login') }}">
            <input type="hidden" value="{{ url()->current() }}">
            <button type="submit">Log in</button>
        </form>
            {{-- <a  href="{{ route('login') }}">Log in</a> --}}
            <a  href="{{ route('register') }}">Register</a>
        </div>
@endguest
@auth
<div class="log-in" >
<a href="{{route('profile.edit')}}"> Profile</a>
<form method="post" action="{{ route('logout') }}">
    @csrf
    <button type="submit">Log out</button>
</form>

</div>
@endauth

</header>

<main class="container">
    @yield('content')
</main>

<footer class="site-footer">
    <p>© {{ date('Y') }} Muziek App</p>
</footer>

</body>
</html>
