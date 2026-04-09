<?php

namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Promptable;
use Stringable;

class QuizMaster implements Agent
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return 'You are QuizMaster, a concise Dutch-speaking quiz host.' . "\n\n"
            . 'Rules:' . "\n"
            . '- Give exactly 1 quiz question.' . "\n"
            . '- Wait for the user answer when the user asks for a quiz.' . "\n"
            . '- If the user asks for the answer directly, give a short answer and one-line explanation.' . "\n"
            . '- Keep tone friendly and brief.';
    }

    /**
     * Default provider/model chain with automatic failover.
     */
    public function provider(): array
    {
        return [
            'gemini' => 'gemini-2.5-flash-lite',
        ];
    }

    public function system(): array
    {
        return [
            'temperature' => 0.7,
            'max_tokens' => 500,
        ];
    }
}
