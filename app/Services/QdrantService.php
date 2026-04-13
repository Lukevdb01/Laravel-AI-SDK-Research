<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class QdrantService
{
    public function createCollection($collectionName, $vectorSize = 1536)
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
        Http::put(config('app.qdrant_host_url')."/collections/{$collectionName}/points", [
            'points' => [
                [
                    'id' => $pointId,
                    'vector' => $vector,
                    'payload' => $payload,
                ],
            ],
        ]);
    }

    public function searchVector($collectionName, $vector, $limit = 5)
    {
        return Http::post(config('app.qdrant_host_url')."/collections/{$collectionName}/points/search", [
            'vector' => $vector,
            'limit' => $limit,
        ])->json();
    }
}
