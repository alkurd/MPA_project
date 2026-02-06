<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\SongController; // Die maken we hierna

// Overzicht van alle genres
Route::get('/', [GenreController::class, 'index'])->name('home');
Route::get('/genres', [GenreController::class, 'index'])->name('genres');

// Overzicht van liedjes binnen een specifiek genre
Route::get('/genres/{genre}', [GenreController::class, 'show'])->name('genres.show');

Route::get('/playlist', [PlaylistController::class, 'index'])->name('playlist.index');
Route::post('/playlist/add/{id}', [PlaylistController::class, 'add'])->name('playlist.add');
Route::delete('/playlist/{id}', [PlaylistController::class, 'delete'])->name('playlist.delete');

// overzicht van profile functies
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
Route::post('/playlist/save',[PlaylistController::class,'save'])->name('save')->middleware('auth');





// Detailpagina van een specifiek liedje
Route::get('/songs/{song}', [SongController::class, 'show'])->name('songs.show');

Route::get('/playlist/playlists', [PlaylistController::class, 'show'])->name('playlists');

// Routes voor ingelogde gebruikers om nummers toe te voegen aan playlists
Route::middleware('auth')->group(function () {
    Route::post('/playlist/add-to-existing/{id}', [PlaylistController::class, 'addToExistingPlaylist'])->name('playlist.add-to-existing');
    Route::post('/playlist/add-to-new/{id}', [PlaylistController::class, 'addToNewPlaylist'])->name('playlist.add-to-new');
});


require __DIR__.'/auth.php';
