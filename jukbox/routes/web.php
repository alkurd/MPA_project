<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GenreController;
use App\Http\Controllers\SongController; // Die maken we hierna

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Overzicht van alle genres
Route::get('/genres', [GenreController::class, 'index'])->name('genres.index');

// Overzicht van liedjes binnen een specifiek genre
Route::get('/genres/{genre}', [GenreController::class, 'show'])->name('genres.show');

// Detailpagina van een specifiek liedje
Route::get('/songs/{song}', [SongController::class, 'show'])->name('songs.show');
use App\Http\Controllers\PlaylistController;

Route::post('/playlist/add/{id}', [PlaylistController::class, 'add'])->name('playlist.add');

require __DIR__.'/auth.php';
