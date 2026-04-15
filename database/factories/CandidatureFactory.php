<?php

namespace Database\Factories;

use App\Models\Candidature;
use App\Models\Profil;
use App\Models\Offre;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Candidature>
 */
class CandidatureFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'offre_id' => Offre::factory(),
            'profil_id' => Profil::factory(),
            'message' => fake()->paragraph(),
            'statut' => fake()->randomElement(['en_attente', 'acceptee', 'refusee']),
        ];
    }
}
