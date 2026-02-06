# Uitleg Nieuwe Code - Playlist Functionaliteit

## 📋 Inhoudsopgave
1. [PlaylistController.php - Nieuwe Methoden](#playlistcontroller)
2. [GenreController.php - Aanpassingen](#genrecontroller)
3. [genres/show.blade.php - View Aanpassingen](#view)
4. [web.php - Routes](#routes)
5. [Gevonden Fouten & Verbeteringen](#fouten)

---

## 1. PlaylistController.php - Nieuwe Methoden {#playlistcontroller}

### Methode: `addToExistingPlaylist()`

```php
public function addToExistingPlaylist(Request $request, $id){
```
**Uitleg**: Nieuwe publieke methode die een nummer toevoegt aan een bestaande playlist.
- `Request $request`: Bevat formulierdata (playlist_id)
- `$id`: Het song ID uit de URL parameter

**Probleem**: Geen type hints voor return type. Beter: `public function addToExistingPlaylist(Request $request, int $id): RedirectResponse`

---

```php
    if(!auth()->check()){
        return redirect()->route('login');
    }
```
**Uitleg**: Controleert of gebruiker ingelogd is.
**Probleem**: 
- Deze check is **overbodig** omdat de route al `middleware('auth')` heeft
- Dubbele check = onnodige code
**Beter**: Verwijder deze check, middleware doet dit al

---

```php
    $song = Song::findOrFail($id);
```
**Uitleg**: Haalt het nummer op, gooit 404 als niet gevonden.
**Goed**: Correct gebruik van `findOrFail()`

---

```php
    $request->validate([
        'playlist_id' => 'required|exists:playlists,id'
    ]);
```
**Uitleg**: Valideert dat playlist_id bestaat.
**Probleem**: 
- Valideert NIET dat de playlist van de ingelogde gebruiker is!
- Iemand kan een playlist_id van een andere gebruiker gebruiken
**Beter**: 
```php
'playlist_id' => ['required', 'exists:playlists,id', Rule::exists('playlists', 'id')->where('user_id', auth()->id())]
```

---

```php
    $playlist = Playlist::where('user_id', auth()->id())
        ->findOrFail($request->playlist_id);
```
**Uitleg**: Haalt playlist op en controleert ownership.
**Goed**: ✅ Correct - controleert dat playlist van gebruiker is
**Kleine verbetering**: Kan korter:
```php
$playlist = Playlist::where('user_id', auth()->id())
    ->findOrFail($request->playlist_id);
```

---

```php
    // Check if song already exists in playlist
    if($playlist->songs()->where('song_id', $song->id)->exists()){
        return back()->with('info', 'Dit nummer bestaat al in deze playlist');
    }
```
**Uitleg**: Controleert of nummer al in playlist zit.
**Probleem**: 
- `song_id` is niet de juiste kolomnaam in de pivot tabel
- Laravel gebruikt standaard `song_id`, maar check de migration!
**Beter**: 
```php
if($playlist->songs()->where('songs.id', $song->id)->exists()){
```
Of gebruik de relationship direct:
```php
if($playlist->songs->contains($song->id)){
```

---

```php
    $playlist->songs()->attach($song->id);
```
**Uitleg**: Voegt nummer toe aan many-to-many relatie.
**Goed**: ✅ Correct gebruik van `attach()`

---

```php
    return redirect()->back()->with('success', 'Song is toegevoegd aan de playlist');
```
**Uitleg**: Redirect terug met success message.
**Goed**: ✅ Correct

---

### Methode: `addToNewPlaylist()`

```php
public function addToNewPlaylist(Request $request, $id){
```
**Zelfde opmerkingen als hierboven over type hints**

---

```php
    if(!auth()->check()){
        return redirect()->route('login');
    }
```
**Zelfde probleem**: Overbodig door middleware

---

```php
    $request->validate([
        'naam' => 'required|string|max:255'
    ]);
```
**Uitleg**: Valideert playlist naam.
**Probleem**: 
- Geen validatie dat naam uniek is per gebruiker
- Gebruiker kan meerdere playlists metzelfde naam maken
**Beter**:
```php
'naam' => ['required', 'string', 'max:255', Rule::unique('playlists', 'naam')->where('user_id', auth()->id())]
```

---

```php
    $playlist = Playlist::create([
        'user_id' => auth()->id(),
        'naam' => $request->naam,
    ]);
```
**Uitleg**: Maakt nieuwe playlist aan.
**Goed**: ✅ Correct gebruik van mass assignment

---

```php
    $playlist->songs()->attach($song->id);
```
**Goed**: ✅ Correct

---

## 2. GenreController.php - Aanpassingen {#genrecontroller}

### Nieuwe Imports

```php
use App\Models\Playlist;
use Illuminate\Support\Facades\Auth;
```
**Uitleg**: Nodig voor playlists ophalen en auth check.
**Goed**: ✅ Correct

---

### Aanpassing in `show()` methode

```php
    $playlists = [];
    $hasSessionPlaylist = false;
```
**Uitleg**: Initialiseert variabelen voor niet-ingelogde gebruikers.
**Goed**: ✅ Goede default waarden

---

```php
    if(Auth::check()){
        $playlists = Playlist::where('user_id', Auth::id())->get();
        $sessionPlaylist = session()->get('playlist', []);
        $hasSessionPlaylist = !empty($sessionPlaylist);
    }
```
**Uitleg**: Haalt playlists op en checkt session playlist.
**Problemen**:
1. **Inconsistent gebruik**: `Auth::check()` en `Auth::id()` - beter consistent
2. **Performance**: `->get()` haalt ALLE playlists op, ook als er geen zijn
3. **Session check**: Checkt alleen of array niet leeg is, niet of er echt items in zitten

**Beter**:
```php
if(auth()->check()){
    $playlists = Playlist::where('user_id', auth()->id())->get();
    $sessionPlaylist = session()->get('playlist', []);
    $hasSessionPlaylist = count($sessionPlaylist) > 0;
}
```

**Nog beter** (performance):
```php
if(auth()->check()){
    $playlists = auth()->user()->playlists; // Via relationship
    $hasSessionPlaylist = session()->has('playlist') && count(session('playlist', [])) > 0;
}
```

---

```php
    return view('genres.show',compact('genre','songs', 'playlists', 'hasSessionPlaylist'));
```
**Uitleg**: Geeft variabelen door aan view.
**Goed**: ✅ Correct

---

## 3. genres/show.blade.php - View Aanpassingen {#view}

### Button voor ingelogde gebruikers

```php
@auth
<button type="button" class="btn-add-song" data-song-id="{{ $song->id }}" onclick="openAddModal({{ $song->id }})">
    + Toevoegen
</button>
@endauth
```
**Uitleg**: Button die modal opent voor ingelogde gebruikers.
**Problemen**:
1. **Inline onclick**: Niet best practice, beter via event listener
2. **data-song-id**: Wordt niet gebruikt, kan weg
3. **Mixing PHP en JS**: `onclick="openAddModal({{ $song->id }})"` kan XSS problemen geven

**Beter**:
```php
<button type="button" class="btn-add-song" data-song-id="{{ $song->id }}">
    + Toevoegen
</button>
```
En dan in JavaScript:
```javascript
document.querySelectorAll('.btn-add-song').forEach(button => {
    button.addEventListener('click', function() {
        openAddModal(this.dataset.songId);
    });
});
```

---

### Modal Forms

```php
<form action="" method="POST" id="addToSessionForm" data-route="{{ route('playlist.add', 0) }}">
```
**Uitleg**: Form met lege action, wordt via JS gevuld.
**Problemen**:
1. **Route met 0**: Hacky manier om route template te krijgen
2. **Lege action**: Form werkt niet zonder JavaScript (accessibility probleem)
3. **data-route**: Wordt gebruikt om route te reconstrueren

**Beter**: Gebruik named routes met parameters:
```php
<form action="{{ route('playlist.add', ['id' => $song->id]) }}" method="POST">
```
Maar dit werkt niet omdat we het dynamisch moeten maken...

**Alternatief**: Gebruik een hidden input:
```php
<input type="hidden" name="song_id" value="{{ $song->id }}">
```
En pas de controller aan om `song_id` uit request te halen.

---

### JavaScript: `openAddModal()`

```javascript
function openAddModal(songId) {
    const modal = document.getElementById('addToPlaylistModal');
    const sessionForm = document.getElementById('addToSessionForm');
    const existingForm = document.getElementById('addToExistingForm');
    const newForm = document.getElementById('addToNewForm');
```
**Uitleg**: Haalt DOM elementen op.
**Probleem**: 
- Geen null checks - kan errors geven als elementen niet bestaan
- Globale functie (pollution van global scope)

**Beter**:
```javascript
function openAddModal(songId) {
    const modal = document.getElementById('addToPlaylistModal');
    if (!modal) return; // Safety check
    
    const sessionForm = document.getElementById('addToSessionForm');
    const existingForm = document.getElementById('addToExistingForm');
    const newForm = document.getElementById('addToNewForm');
```

---

```javascript
    if(sessionForm) {
        const route = sessionForm.getAttribute('data-route');
        sessionForm.action = route.replace('/0', '/' + songId);
    }
```
**Uitleg**: Vervangt placeholder 0 met echte song ID.
**Problemen**:
1. **String replace hack**: Als route `/playlist/add/10` is, wordt het `/playlist/add/110` (vervangt eerste 0)
2. **Geen error handling**: Als route niet bestaat, crasht het

**Beter**:
```javascript
if(sessionForm) {
    const route = sessionForm.getAttribute('data-route');
    sessionForm.action = route.replace(/\/0$/, '/' + songId); // Regex: alleen laatste /0
}
```

**Nog beter**: Gebruik URL constructor:
```javascript
const baseUrl = sessionForm.getAttribute('data-route');
const url = new URL(baseUrl, window.location.origin);
url.pathname = url.pathname.replace('/0', '/' + songId);
sessionForm.action = url.pathname;
```

---

### CSS Styling

```css
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
```
**Uitleg**: Modal styling.
**Probleem**: 
- `z-index: 1000` kan conflicteren met andere modals
- Geen `!important` op display, kan overschreven worden

**Beter**: Gebruik een CSS variabele voor z-index:
```css
:root {
    --modal-z-index: 1000;
}
.modal {
    z-index: var(--modal-z-index);
}
```

---

## 4. web.php - Routes {#routes}

```php
Route::middleware('auth')->group(function () {
    Route::post('/playlist/add-to-existing/{id}', [PlaylistController::class, 'addToExistingPlaylist'])->name('playlist.add-to-existing');
    Route::post('/playlist/add-to-new/{id}', [PlaylistController::class, 'addToNewPlaylist'])->name('playlist.add-to-new');
});
```
**Uitleg**: Nieuwe routes voor ingelogde gebruikers.
**Goed**: ✅ Correct gebruik van middleware group
**Kleine verbetering**: Kan bij andere auth routes staan voor betere organisatie

---

## 5. Gevonden Fouten & Verbeteringen {#fouten}

### 🔴 Kritieke Fouten

1. **Security Issue**: `addToExistingPlaylist()` valideert niet dat playlist van gebruiker is in validation
2. **Security Issue**: Geen ownership check voordat playlist wordt gebruikt
3. **Bug**: String replace in JavaScript kan verkeerde URLs maken (`/0` in `/10` wordt `/110`)

### 🟡 Medium Problemen

4. **Overbodige code**: Auth checks in controllers terwijl middleware dit al doet
5. **Performance**: `->get()` haalt alle playlists op zonder limit
6. **Inconsistentie**: Mix van `Auth::` en `auth()` helper
7. **Accessibility**: Forms werken niet zonder JavaScript

### 🟢 Kleine Verbeteringen

8. **Code kwaliteit**: Geen type hints voor return types
9. **Code kwaliteit**: Inline onclick handlers
10. **Code kwaliteit**: Geen null checks in JavaScript
11. **UX**: Geen loading states bij form submissions
12. **Validation**: Geen unique check voor playlist namen

---

## 📝 Aanbevolen Verbeteringen

### 1. Fix Security Issues

```php
// In PlaylistController
use Illuminate\Validation\Rule;

$request->validate([
    'playlist_id' => [
        'required',
        Rule::exists('playlists', 'id')->where('user_id', auth()->id())
    ]
]);
```

### 2. Fix JavaScript Bug

```javascript
sessionForm.action = route.replace(/\/0$/, '/' + songId);
```

### 3. Verwijder Overbodige Auth Checks

```php
// Verwijder deze regels uit beide nieuwe methoden:
if(!auth()->check()){
    return redirect()->route('login');
}
```

### 4. Gebruik Relationships

```php
// In GenreController
$playlists = auth()->user()->playlists;
```

### 5. Betere JavaScript Structuur

```javascript
(function() {
    'use strict';
    
    function openAddModal(songId) {
        // ... code
    }
    
    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.btn-add-song').forEach(button => {
            button.addEventListener('click', function() {
                openAddModal(this.dataset.songId);
            });
        });
    });
})();
```

---

## ✅ Wat Goed Is

1. ✅ Correct gebruik van Laravel relationships
2. ✅ Goede scheiding tussen guest en auth functionaliteit
3. ✅ Gebruik van session voor tijdelijke playlists
4. ✅ Duidelijke error messages
5. ✅ Goede route naming conventions

