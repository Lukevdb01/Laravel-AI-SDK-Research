<?php

namespace App\Http\Controllers;

use App\Models\Logging;
use Inertia\Inertia;
use JsonException;

class LoggingController extends Controller
{
    public function history()
    {
        $logs = Logging::query()
            ->latest('id')
            ->limit(100)
            ->get()
            ->map(fn (Logging $log) => [
                'id' => $log->id,
                'created_at' => optional($log->created_at)->format('Y-m-d H:i:s'),
                'ai_module_used' => $log->ai_module_used,
                'prompt' => $log->prompt,
                'total_tokens_used' => $log->total_tokens_used,
                'response' => $this->formatResponseForDisplay($log->response),
            ])
            ->values();

        return Inertia::render('History', [
            'logs' => $logs,
        ]);
    }

    private function normalizeNewlines($data)
    {
        if (is_array($data)) {
            return array_map([$this, 'normalizeNewlines'], $data);
        }

        if (is_string($data)) {
            return preg_replace("/\r\n|\r|\n/", "\n", $data);
        }

        return $data;
    }

    private function formatResponseForDisplay($response): string
    {
        $cleaned = $this->decodeJsonStrings($response);
        $normalized = $this->normalizeNewlines($cleaned);

        if (is_array($normalized)) {
            $encoded = json_encode($normalized, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

            return $encoded === false ? '[]' : $encoded;
        }

        if (is_string($normalized)) {
            return $normalized;
        }

        $encoded = json_encode($normalized, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);

        return $encoded === false ? '' : $encoded;
    }

    private function decodeJsonStrings($data)
    {
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                $data[$key] = $this->decodeJsonStrings($value);
            }

            return $data;
        }

        if (! is_string($data)) {
            return $data;
        }

        $trimmed = trim($data);
        if ($trimmed === '') {
            return $data;
        }

        $startsLikeJson = str_starts_with($trimmed, '{') || str_starts_with($trimmed, '[');
        if (! $startsLikeJson) {
            return $data;
        }

        try {
            $decoded = json_decode($trimmed, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return $data;
        }

        return $this->decodeJsonStrings($decoded);
    }
}
