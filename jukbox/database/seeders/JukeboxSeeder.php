<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Genre;
use App\Models\Song;

class JukeboxSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // genre maken
        $rock = Genre::create(['naam' => 'Rock']);
        $pop = Genre::create(['naam' => 'Pop']);
        $techno = Genre::create(['naam' => 'Techno']);
        $jazz = Genre::create(['naam' => 'Jazz']);
        $hiphop = Genre::create(['naam' => 'Hip Hop']);

        Song::create([
            'genre_id' => $rock->id,
            'naam' => 'Bohemian Rhapsody',
            'artist' => 'Queen',
            'duration' => 354 //seconden
        ]);
        Song::create([
            'genre_id' => $rock->id,
            'naam' => 'Stairway to Heaven',
            'artist' => 'Led Zeppelin',
            'duration' => 482
        ]);
        /////
        Song::create([
            'genre_id' => $pop->id,
            'naam' => 'Blinding Lights',
            'artist' => 'The Weeknd',
            'duration' => 200
        ]);
        Song::create([
            'genre_id' => $pop->id,
            'naam' => 'Shape of You',
            'artist' => 'Ed Sheeran',
            'duration' => 233
        ]);
        Song::create([
            'genre_id' => $techno->id,
            'naam' => 'Spastik — Plastikman',
            'artist' => 'Richie Hawtin',
            'duration' => 562
        ]);
        Song::create([
            'genre_id' => $techno->id,
            'naam' => 'Strings of Life — Rhythim Is Rhythim',
            'artist' => 'Derrick May',
            'duration' => 443
        ]);
        Song::create([
            'genre_id' => $jazz->id,
            'naam' => 'So What',
            'artist' => 'Miles Davis',
            'duration' => 562
        ]);
        Song::create([
            'genre_id' => $jazz->id,
            'naam' => 'Take Five',
            'artist' => 'The Dave Brubeck Quartet',
            'duration' => 324
        ]);
        Song::create([
            'genre_id' => $hiphop->id,
            'naam' => 'Juicy',
            'artist' => 'The Notorious B.I.G.',
            'duration' => 302
        ]);
        Song::create([
            'genre_id' => $hiphop->id,
            'naam' => 'N.Y. State of Mind',
            'artist' => 'Nas',
            'duration' => 294
        ]);
    }
}





