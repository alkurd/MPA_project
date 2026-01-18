<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Muziek App')</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<header class="site-header">
    <h1>Muziek Bibliotheek</h1>
    <nav>
        {{-- <a href="{{ route('genres.index') }}">Genres</a> --}}
    </nav>
</header>

<main class="container">
    @yield('content')
</main>

<footer class="site-footer">
    <p>© {{ date('Y') }} Muziek App</p>
</footer>

</body>
</html>
