<?php

namespace App\Http\Controllers;

use App\Ai\Agents\QuizMaster;
use App\Models\User;
use Throwable;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Ai\Responses\StructuredAgentResponse;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Dashboard', [
            'dataset' => $this->dataset_builder()
        ]);
    }

    public function aiRequestHandler(Request $request)
    {
        try {
            $agent = new QuizMaster;
            $prompt = 'Gebruik je eigen agent instructions als baseline richtlijn. '
                . 'Maak een korte recap-quiz (maximaal 3 vragen) op basis van alle deelnemers en alle activiteiten uit de dataset hieronder. '
                . 'Reageer volgens het schema van de agent met de velden questions, options en answers. '
                . 'Zorg dat dezelfde index bij elkaar hoort (questions[i], options[i], answers[i]).'
                . "\n\nDataset (JSON):\n"
                . json_encode($this->dataset_builder(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            $response = $agent->prompt(prompt: $prompt, provider: $agent->provider());

            $payload = $response instanceof StructuredAgentResponse
                ? $response->toArray()
                : ['text' => $response->text];

            return response()->json([
                'ok' => true,
                'response' => $payload,
            ]);
        } catch (Throwable $e) {
            return response()->json([
                'ok' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function dataset_builder()
    {
        $users_with_scores = User::with('scores')->get();
        $location_information = [
            [
                'location' => 'Noordkade Uitjes, Verlengde Noordkade 4a, 5462 EH Veghel',
                'name' => 'Axe Throwing Veghel',
                'activity_type' => 'axe-throwing',
                'description' => '60 minuten bijlwerpen met begeleiding van Axeperts. Geschikt voor teamuitjes vanaf 16 jaar, tot 24 personen.',
                'duration_minutes' => 60,
                'age_min' => 16,
                'indoor' => true,
                'group_max' => 24,
            ],
            [
                'location' => 'Noordkade Uitjes, Verlengde Noordkade 4a, 5462 EH Veghel',
                'name' => 'Sim Racing Veghel',
                'activity_type' => 'sim-racing',
                'description' => 'Race op Formule 1-circuits met 20 racesimulatoren. Voor teamuitjes en kinderfeestjes.',
                'duration_minutes' => 60,
                'age_min' => 0,
                'indoor' => true,
                'group_max' => 20,
            ],
            [
                'location' => 'Noordkade Uitjes, Verlengde Noordkade 4a, 5462 EH Veghel',
                'name' => 'Tennis',
                'activity_type' => 'tennis',
                'description' => 'Fictieve activiteit: speel een energieke tennis challenge op de Noordkade met mini-toernooi en begeleiding.',
                'duration_minutes' => 75,
                'age_min' => 10,
                'indoor' => false,
                'group_max' => 16,
            ],
        ];

        return [
            'users_with_scores' => $users_with_scores,
            'location_information' => $location_information,
        ];
    }
}
