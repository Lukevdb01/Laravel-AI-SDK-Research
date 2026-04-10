<?php

namespace App\Http\Controllers;

use App\Ai\Agents\QuizMaster;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Ai\Responses\StructuredAgentResponse;
use Throwable;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard');
    }

    public function aiRequestHandler(Request $request)
    {
        try {
            $prompt = 'JSON DATASET: '.json_encode($this->datasetBuilder(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $response = (new QuizMaster)->prompt(prompt: $prompt);

            $payload = $response instanceof StructuredAgentResponse ? $response->toArray() : ['text' => $response->text];

            return response()->json(['ok' => true, 'response' => $payload], 200);
        } catch (Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function datasetBuilder()
    {
        $user_and_score = User::query()->select('users.id', 'users.name')
            ->with([
                'scores' => function ($query) {
                    $query->select('id', 'user_id', 'score', 'activity_type');
                },
            ])->get()->map(function (User $user) {
                return [
                    'user_data' => [
                        'username' => $user->name,
                    ],
                    'scores' => $user->scores->map(function ($score) {
                        return [
                            'score' => $score->score,
                            'activity-type' => $score->activity_type,
                        ];
                    })->values(),
                ];
            })->values();

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

        return [
            'user_and_score' => $user_and_score,
            'location_information' => $location_information,
        ];
    }
}
