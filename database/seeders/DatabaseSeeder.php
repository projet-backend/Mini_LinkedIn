<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Offre;
use App\Models\Competence;
use App\Models\Profil;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
    

        User::factory(2)->admin()->create();

        User::factory(5)->recruteur()->create()->each(function ($user){
            Offre::factory(rand(2,3))->create(['user_id' => $user->id]);
        });

        $competences = Competence::factory(10)->create();

        User::factory(10)->candidat()->create()->each(function ($user) use ($competences){
            $profil = Profil::factory()->create(['user_id' => $user->id]);
            $selected = $competences->random(3);
            $attachData = $selected->mapWithKeys(function($competence){
                return [$competence->id => ['niveau' => fake()->randomElement(['débutant', 'intermédiaire', 'expert'])]];
            })->toArray();
                
            $profil->Competences()->attach($attachData);
        });
    }
}
