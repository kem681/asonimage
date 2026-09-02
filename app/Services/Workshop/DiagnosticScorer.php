<?php

namespace App\Services\Workshop;

use InvalidArgumentException;

/**
 * Calcule, a partir des 24 reponses, un score de manquement par axe (0 a 100)
 * et les axes en tete (plusieurs en cas d'egalite : c'est alors a
 * l'utilisateur de choisir son axe phare).
 */
class DiagnosticScorer
{
    public function __construct(private WorkshopContent $content) {}

    /**
     * @param  array<int|string, int|string>  $answers  index de l'affirmation => valeur de l'echelle
     * @return array{scores: array<string, int>, leading: list<string>}
     */
    public function score(array $answers): array
    {
        $statements = $this->content->statements();
        $min = $this->content->scaleMin();
        $max = $this->content->scaleMax();

        $sums = array_fill_keys($this->content->axisKeys(), 0);
        $counts = array_fill_keys($this->content->axisKeys(), 0);

        foreach ($statements as $index => $statement) {
            if (! array_key_exists($index, $answers) || $answers[$index] === null || $answers[$index] === '') {
                throw new InvalidArgumentException("Réponse manquante pour l'affirmation {$index}.");
            }

            $value = (int) $answers[$index];

            if ($value < $min || $value > $max) {
                throw new InvalidArgumentException("Réponse hors échelle pour l'affirmation {$index}.");
            }

            $sums[$statement['axis']] += $value;
            $counts[$statement['axis']]++;
        }

        $scores = [];
        foreach ($sums as $axis => $sum) {
            $count = $counts[$axis];
            $range = $count * ($max - $min);
            $scores[$axis] = $range > 0 ? (int) round(($sum - $count * $min) / $range * 100) : 0;
        }

        $top = max($scores);
        $leading = array_values(array_keys(array_filter($scores, fn (int $score) => $score === $top)));

        return ['scores' => $scores, 'leading' => $leading];
    }
}
