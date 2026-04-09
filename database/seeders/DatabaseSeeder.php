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

        Scores::create([
            'user_id' => $user->id,
            'score' => 8,
            'max_score' => 10,
            'activity_type' => 'quiz',
            'location' => 'noordkade-uitjes',
        ]);

        Scores::create([
            'user_id' => $user->id,
            'score' => 6,
            'max_score' => 10,
            'activity_type' => 'challenge',
            'location' => 'noordkade-uitjes',
        ]);

        Scores::create([
            'user_id' => $user->id,
            'score' => 9,
            'max_score' => 10,
            'activity_type' => 'round',
            'location' => 'noordkade-uitjes',
        ]);
    }
}
