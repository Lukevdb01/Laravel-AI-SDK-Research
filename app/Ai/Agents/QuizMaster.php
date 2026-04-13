<?php

namespace App\Ai\Agents;

use App\Ai\Middleware\LogPrompts;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasMiddleware;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Stringable;

class QuizMaster implements Agent, HasMiddleware, HasStructuredOutput
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
                ->description('Maximum 3 quiz questions.'),
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
                ->description('Multiple choice options per question. Index must match questions.'),
            'answers' => $schema->array()
                ->min(1)
                ->max(3)
                ->items($schema->string())
                ->required()
                ->description('Correct answer per question. Index must match questions.'),
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
