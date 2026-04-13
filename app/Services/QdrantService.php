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

    public function upsertVector($collectionName, $pointId, $vector, $payload)
    {
        $response = Http::put(
            "http://localhost:6333/collections/{$collectionName}/points",
            [
                'points' => [
                    [
                        'id' => $pointId,
                        'vector' => $vector,
                        'payload' => $payload,
                    ],
                ],
            ]
        )->json();

        // Debug: check response
        if (isset($response['status']['error'])) {
            dd('Qdrant error:', $response['status']['error']);
        }

        return $response;
    }

    public function semanticSearch(string $collectionName, array $vector, array $activity_types = [], int $limit = 10): array
    {
        $payload = [
            'vector' => $vector,
            'limit' => $limit,
            'with_payload' => true,
        ];

        // Optional: filter op activity_types PLUS semantic search
        if (!empty($activity_types)) {
            $payload['filter'] = [
                'must' => [
                    [
                        'key' => 'activity_type',
                        'match' => [
                            'any' => $activity_types,
                        ],
                    ],
                ],
            ];
        }

        $response = Http::post(
            "http://localhost:6333/collections/{$collectionName}/points/search",
            $payload
        )->json();

        return collect($response['result'] ?? [])->map(function ($item) {
            return array_merge(
                $item['payload'] ?? []
            );
        })->toArray();
    }
}
