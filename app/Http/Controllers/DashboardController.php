<?php

namespace App\Http\Controllers;

use App\Ai\Agents\QuizMaster;
use App\Models\User;
use App\Services\QdrantService;
use Inertia\Inertia;
use Laravel\Ai\Responses\StructuredAgentResponse;
use Throwable;

class DashboardController extends Controller
{
    private QdrantService $qdrant;

    public function __construct()
    {
        $this->qdrant = new QdrantService;
    }

    public function index()
    {
        return Inertia::render('Dashboard', []);
    }

    public function aiRequestHandler()
    {
        try {
            $dataset = [
                'users' => $this->datasetBuilder(),
                'locations' => $this->getAllLocationsFromQdrant(),
            ];

            $prompt = json_encode($dataset, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $response = (new QuizMaster)->prompt(prompt: $prompt);

            $payload = $response instanceof StructuredAgentResponse ? $response->toArray() : ['text' => $response->text];

            return response()->json(['ok' => true, 'response' => $payload], 200);
        } catch (Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function getAllLocationsFromQdrant(): array
    {
        $vector = array_fill(0, 768, 0.1);

        return $this->qdrant->searchVector('locations', $vector, limit: 100);
    }

    private function datasetBuilder()
    {
        return User::query()->select('users.id', 'users.name')
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
                            'activity_type' => $score->activity_type,
                        ];
                    })->values(),
                ];
            })->values();
    }
}
