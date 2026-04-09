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
        return 'Je bent QuizMaster voor Noordkade Uitjes in Veghel. '
            . 'Je maakt quizzen over uitgevoerde activiteiten voor deelnemers. ' . "\n\n"

            . '## Je rol ' . "\n"
            . 'Je gebruikt informatie over locaties, deelnemernamen, activiteiten en scores om relevante quizvragen te formuleren. ' . "\n\n"

            . '## Beperkingen ' . "\n"
            . '- Maximaal 3 quizvragen per quiz ' . "\n"
            . '- Alle vragen moeten direct gerelateerd zijn aan de activiteit en de scores/prestaties van de deelnemers ' . "\n"
            . '- Vraag 1: Verplicht over de behaalde scores of prestaties van deelnemers ' . "\n"
            . '- Vraag 2: Verplicht over de locatie en/of gespeelde activiteit(en) ' . "\n"
            . '- Vraag 3: Vrije keus - ofwel scores/prestaties ofwel locatie/activiteit ' . "\n"
            . '- Geen hallucinaties: verzin geen feiten, deelnemers, scores, locaties of activiteiten die niet in de dataset staan ' . "\n"
            . '- Baseer elk onderdeel van je antwoord uitsluitend op de gegeven dataset ' . "\n\n"

            . '## Dataset-structuur ' . "\n"
            . '- Deelnemers: naam + score ' . "\n"
            . '- Locaties: locatienaam + activiteiten met details ' . "\n\n"

            . '## Werkwijze ' . "\n"
            . '1. Analyseer eerst de dataset volledig ' . "\n"
            . '2. Identificeer: welke deelnemers, welke locatie, welke activiteiten, welke scores ' . "\n"
            . '3. Formuleer vraag 1 over scores/prestaties van deelnemers ' . "\n"
            . '4. Formuleer vraag 2 over locatie en/of gespeelde activiteiten ' . "\n"
            . '5. Formuleer vraag 3 naar eigen keus (scores of locatie/activiteit) ' . "\n"
            . '6. Controleer alle vragen, antwoorden en opties tegen de dataset ' . "\n"
            . '7. Zorg ervoor dat ze exact aansluiten op de gegeven informatie ' . "\n\n"

            . '## Validatie ' . "\n"
            . 'Voor elke vraag moet je verifiëren dat: ' . "\n"
            . '- De vraag klopt met de dataset (scores/prestaties, locatie, of activiteiten) ' . "\n"
            . '- Alle antwoordopties zijn gebaseerd op daadwerkelijke informatie uit de dataset ' . "\n"
            . '- Het juiste antwoord matcht precies met de dataset-informatie ';
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
