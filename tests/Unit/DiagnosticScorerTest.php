<?php

namespace Tests\Unit;

use App\Services\Workshop\DiagnosticScorer;
use App\Services\Workshop\WorkshopContent;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class DiagnosticScorerTest extends TestCase
{
    private function scorer(): DiagnosticScorer
    {
        return new DiagnosticScorer(new WorkshopContent(__DIR__.'/../../resources/content/3x30.php'));
    }

    private function answersByAxis(array $values): array
    {
        $content = new WorkshopContent(__DIR__.'/../../resources/content/3x30.php');
        $answers = [];
        foreach ($content->statements() as $index => $statement) {
            $answers[$index] = $values[$statement['axis']];
        }

        return $answers;
    }

    public function test_le_contenu_a_bien_huit_affirmations_par_axe(): void
    {
        $content = new WorkshopContent(__DIR__.'/../../resources/content/3x30.php');
        $counts = array_count_values(array_column($content->statements(), 'axis'));

        $this->assertSame(['filiation' => 8, 'desert' => 8, 'appel' => 8], array_intersect_key($counts, array_flip(['filiation', 'desert', 'appel'])));
        $this->assertSame(24, $content->statementCount());
    }

    public function test_les_scores_vont_de_zero_a_cent_et_designent_l_axe_phare(): void
    {
        $result = $this->scorer()->score($this->answersByAxis(['filiation' => 1, 'desert' => 4, 'appel' => 2]));

        $this->assertSame(['filiation' => 0, 'desert' => 100, 'appel' => 33], $result['scores']);
        $this->assertSame(['desert'], $result['leading']);
    }

    public function test_une_egalite_renvoie_plusieurs_axes_en_tete(): void
    {
        $result = $this->scorer()->score($this->answersByAxis(['filiation' => 3, 'desert' => 3, 'appel' => 1]));

        $this->assertSame(['filiation', 'desert'], $result['leading']);
    }

    public function test_une_reponse_manquante_est_refusee(): void
    {
        $answers = $this->answersByAxis(['filiation' => 2, 'desert' => 2, 'appel' => 2]);
        unset($answers[5]);

        $this->expectException(InvalidArgumentException::class);

        $this->scorer()->score($answers);
    }

    public function test_une_reponse_hors_echelle_est_refusee(): void
    {
        $answers = $this->answersByAxis(['filiation' => 2, 'desert' => 2, 'appel' => 2]);
        $answers[0] = 9;

        $this->expectException(InvalidArgumentException::class);

        $this->scorer()->score($answers);
    }
}
