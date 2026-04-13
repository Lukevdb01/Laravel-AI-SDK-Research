<?php

namespace App\Ai\Agents;

use App\Ai\Middleware\LogPrompts;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasMiddleware;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class QuizMaster implements Agent, HasStructuredOutput, HasMiddleware
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return file_get_contents(storage_path('app/private/instructions/QUIZ_INSTRUCTION.json'));
    }

    /**
     * Default provider/model chain with automatic failover.
     */
    public function provider(): array
    {
        return [
            'gemini' => 'gemini-3.1-flash-lite-preview',
        ];
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'questions' => $schema->array()
                ->min(1)
                ->max(3)
                ->items($schema->string())
                ->required()
                ->description('Maximaal 3 quizvragen.'),
            'options' => $schema->array()
                ->min(1)
                ->max(3)
                ->items(
                    $schema->array()
                        ->min(2)
                        ->max(6)
                        ->items($schema->string())
                )
                ->required()
                ->description('Meerkeuze-opties per vraag. Index moet overeenkomen met questions.'),
            'answers' => $schema->array()
                ->min(1)
                ->max(3)
                ->items($schema->string())
                ->required()
                ->description('Correct antwoord per vraag. Index moet overeenkomen met questions.'),
        ];
    }

    public function system(): array
    {
        return [
            'temperature' => 0.7,
            'max_tokens' => 500,
        ];
    }

    /**
     * Get the middleware that should run for each prompt.
     */
    public function middleware(): array
    {
        return [
            LogPrompts::class,
        ];
    }
}
