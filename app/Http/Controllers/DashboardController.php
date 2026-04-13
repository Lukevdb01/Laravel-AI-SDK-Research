<?php

namespace App\Http\Controllers;

use App\Ai\Agents\QuizMaster;
use App\Models\User;
use App\Services\QdrantService;
use Illuminate\Support\Facades\Http;
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
            $users = $this->datasetBuilder();
            $activity_types = collect($users)->flatMap(fn ($user) => collect($user['scores'])->pluck('activity_type'))->unique()->toArray();

            $query = implode(', ', $activity_types);
            $query_embedding = $this->getEmbeddingOllama($query);

            $locations = $this->qdrant->semanticSearch('locations', $query_embedding, $activity_types, limit: 100);

            $dataset = [
                'users' => $users,
                'locations' => $locations,
            ];

            $prompt = json_encode($dataset, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            $response = (new QuizMaster)->prompt(prompt: $prompt);

            $payload = $response instanceof StructuredAgentResponse ? $response->toArray() : ['text' => $response->text];

            return response()->json(['ok' => true, 'response' => $payload], 200);
        } catch (Throwable $e) {
            return response()->json(['ok' => false, 'error' => $e->getMessage()], 500);
        }
    }

    private function getEmbeddingOllama(string $text): array
    {
        $response = Http::timeout(60)->post('http://localhost:11434/api/embed', [
            'model' => 'nomic-embed-text',
            'input' => $text,
        ])->json();

        if (isset($response['embeddings']) && is_array($response['embeddings']) && count($response['embeddings']) > 0) {
            return $response['embeddings'][0];
        }

        return [];
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
