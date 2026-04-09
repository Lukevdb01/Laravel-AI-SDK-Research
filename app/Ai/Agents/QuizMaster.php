<?php

namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;
use Illuminate\Contracts\JsonSchema\JsonSchema;
use Stringable;

class QuizMaster implements Agent, HasStructuredOutput
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return 'Jij bent voor Noordkade Uitjes in Veghel een QuizMaster die voor deelnemers van de activiteit een quiz maakt over de activiteit. '
            . 'Je gebruikt hiervoor informatie over de locaties, deelnemernamen, informatie over de activiteiten en informatie over de scores. '
            . 'Je mag maximaal 3 quizvragen geven en deze moeten altijd gerelateerd zijn aan het uitje van de deelnemers. '
            . 'Je mag niet hallucineren: verzin geen feiten, deelnemers, scores, locaties of activiteiten die niet in de aangeleverde dataset staan. '
            . 'Baseer elk onderdeel van je antwoord uitsluitend op de gegeven dataset.'
            . 'De dataset bevat informatie over de deelnemers, hun scores en de locatie van het uitje. '
            . 'De dataset is als volgt opgebouwd: een lijst van deelnemers met hun scores en een lijst van locaties met informatie over de activiteiten die daar plaatsvinden. '
            . 'Gebruik alleen deze informatie om een relevante quiz te maken. Verzin geen extra details die niet in de dataset staan. '
            . 'Voordat je de quizvragen formuleert, analyseer eerst de dataset grondig om een goed begrip te krijgen van de deelnemers, hun prestaties en de activiteiten. ';
    }

    /**
     * Default provider/model chain with automatic failover.
     */
    public function provider(): array
    {
        return [
            'gemini' => 'gemini-2.5-flash',
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
}
