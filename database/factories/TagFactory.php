<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tag>
 */
class TagFactory extends Factory
{
    public const NAMES = [
        'Rock',
        'Pop',
        'Jazz',
        'Blues',
        'Hip-Hop',
        'Rap',
        'Classical',
        'Electronic',
        'Dance',
        'Reggae',
        'Country',
        'R&B',
        'Soul',
        'Metal',
        'Punk',
        'Folk',
        'Techno',
        'House',
        'Dubstep',
        'Trance',
        'Indie',
        'Alternative',
        'K-Pop',
        'Latin',
        'Afrobeat',
        'Lo-fi',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            "name" => $this->faker->randomElement(self::NAMES),
        ];
    }
}
