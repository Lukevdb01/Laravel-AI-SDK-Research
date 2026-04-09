<?php

namespace Database\Seeders;

use App\Models\Scores;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create()
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $jasper = User::factory()->create([
            'name' => 'Jasper',
            'email' => 'jasper@example.com',
        ]);

        Scores::create([
            'user_id' => $user->id,
            'score' => 8,
            'max_score' => 10,
            'activity_type' => 'sim-racing',
            'location' => 'noordkade-uitjes',
        ]);

        Scores::create([
            'user_id' => $user->id,
            'score' => 6,
            'max_score' => 10,
            'activity_type' => 'axe-throwing',
            'location' => 'noordkade-uitjes',
        ]);

        Scores::create([
            'user_id' => $user->id,
            'score' => 9,
            'max_score' => 20,
            'activity_type' => 'tennis',
            'location' => 'noordkade-uitjes',
        ]);

        Scores::create([
            'user_id' => $jasper->id,
            'score' => 7,
            'max_score' => 10,
            'activity_type' => 'sim-racing',
            'location' => 'noordkade-uitjes',
        ]);

        Scores::create([
            'user_id' => $jasper->id,
            'score' => 5,
            'max_score' => 10,
            'activity_type' => 'axe-throwing',
            'location' => 'noordkade-uitjes',
        ]);

        Scores::create([
            'user_id' => $jasper->id,
            'score' => 14,
            'max_score' => 20,
            'activity_type' => 'tennis',
            'location' => 'noordkade-uitjes',
        ]);
    }
}
