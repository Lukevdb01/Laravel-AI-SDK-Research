<?php

namespace Database\Seeders;

use App\Models\Scores;
use App\Models\User;
use App\Services\QdrantService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(QdrantService $qdrant): void
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

        $qdrant->createCollection('locations', 768);
        $this->seedLocationsInVectorDB($qdrant);
    }

    private function seedLocationsInVectorDB(QdrantService $qdrant)
    {
        $location_information = [
            [
                'location' => 'Noordkade Uitjes, Verlengde Noordkade 4a, 5462 EH Veghel',
                'name' => 'Axe Throwing Veghel',
                'activity_type' => 'axe-throwing',
                'description' => '60 minuten bijlwerpen met begeleiding van Axeperts. Geschikt voor teamuitjes vanaf 16 jaar, tot 24 personen.',
                'duration_minutes' => 60,
                'age_min' => 16,
            ],
            [
                'location' => 'Noordkade Uitjes, Verlengde Noordkade 4a, 5462 EH Veghel',
                'name' => 'Sim Racing Veghel',
                'activity_type' => 'sim-racing',
                'description' => 'Race op Formule 1-circuits met 20 racesimulatoren. Voor teamuitjes en kinderfeestjes.',
                'duration_minutes' => 60,
                'age_min' => 0,
            ],
            [
                'location' => 'Noordkade Uitjes, Verlengde Noordkade 4a, 5462 EH Veghel',
                'name' => 'Tennis',
                'activity_type' => 'tennis',
                'description' => 'Fictieve activiteit: speel een energieke tennis challenge op de Noordkade met mini-toernooi en begeleiding.',
                'duration_minutes' => 75,
                'age_min' => 10,
            ],
        ];

        foreach ($location_information as $index => $location) {
            // Generate embedding via Ollama
            $textToEmbed = $location['name'] . '. ' . $location['description'];
            $embedding = $this->getEmbeddingOllama($textToEmbed);

            // Upsert to Qdrant
            $qdrant->upsertVector(
                'locations',
                $index + 1,
                $embedding,
                $location
            );
        }
    }

    private function getEmbeddingOllama(string $text): array
    {
        $response = Http::post('http://localhost:11434/api/embed', [
            'model' => 'nomic-embed-text',
            'input' => $text,
        ])->json();

        return $response['embeddings'][0] ?? [];
    }
}
