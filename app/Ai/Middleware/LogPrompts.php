<?php

namespace App\Ai\Middleware;

use App\Models\Logging;
use Closure;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Ai\Prompts\AgentPrompt;
use Laravel\Ai\Responses\AgentResponse;

class LogPrompts
{
    /**
     * Handle the incoming prompt.
     */
    public function handle(AgentPrompt $prompt, Closure $next)
    {
        Log::channel('laravel')->info('Prompting agent', ['prompt' => $prompt->prompt]);

        $logEntry = Logging::create([
            'ai_module_used' => (string) ($prompt->model ?? $prompt->agent::class),
            'prompt' => $prompt->prompt,
            'total_tokens_used' => 0,
            'response' => [],
        ]);

        try {
            return $next($prompt)->then(function (AgentResponse $response) use ($logEntry) {
                $usage = $response->usage->toArray();

                $totalTokens = (int) (($usage['prompt_tokens'] ?? 0)
                    + ($usage['completion_tokens'] ?? 0)
                    + ($usage['reasoning_tokens'] ?? 0)
                    + ($usage['cache_write_input_tokens'] ?? 0)
                    + ($usage['cache_read_input_tokens'] ?? 0));

                $logEntry->update([
                    'total_tokens_used' => $totalTokens,
                    'response' => [
                        'text' => $response->text,
                        'usage' => $usage,
                        'thinking' => $response->toolCalls
                            ->map(fn ($toolCall) => $toolCall->reasoningSummary ?? null)
                            ->filter(fn ($summary) => filled($summary))
                            ->values()
                            ->all(),
                    ],
                ]);

                Log::channel('laravel')->info('Agent responded', [
                    'text' => $response->text,
                    'usage' => $response->usage,
                ]);

                return $response;
            });
        } catch (\Throwable $e) {
            $logEntry->update([
                'response' => [
                    'error' => $e->getMessage(),
                ],
            ]);

            throw $e;
        }
    }

}
