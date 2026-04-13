<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class QdrantService
{
    public function createCollection($collectionName, $vectorSize = 768): void
    {
        Http::put(config('app.qdrant_host_url')."/collections/{$collectionName}", [
            'vectors' => [
                'size' => $vectorSize,
                'distance' => 'Cosine',
            ],
        ]);
    }

    public function upsertVector($collectionName, $pointId, $vector, $payload): void
    {
        Http::put(
            "http://localhost:6333/collections/{$collectionName}/points",
            [
                'points' => [
                    [
                        'id' => $pointId,
                        'vector' => $vector,
                        'payload' => $payload, // ← Zorg dat dit hier is
                    ],
                ],
            ]
        )->json();
    }

    public function searchVector($collectionName, $vector, $limit = 5): array
    {
        $response = Http::post(
            "http://localhost:6333/collections/{$collectionName}/points/search",
            [
                'vector' => $vector,
                'limit' => $limit,
                'with_payload' => true,
            ]
        )->json();

        // Extract payloads from results
        return collect($response['result'] ?? [])->map(function ($item) {
            return $item['payload'] ?? []; // ← Add payload extraction
        })->toArray();
    }
}
