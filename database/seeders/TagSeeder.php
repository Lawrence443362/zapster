<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            'Rock',
            'Jazz',
            'Hip-Hop',
            'Classical',
            'Pop',
            'Electronic',
            'Reggae',
            'Blues',
            'Metal',
            'Country',
        ];

        foreach ($tags as $name) {
            Tag::create(['name' => $name]);
        }
    }
}
