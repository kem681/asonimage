<?php

namespace Tests\Feature\Workshop;

use App\Models\Diagnostic;
use App\Services\Workshop\WorkshopContent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesWorkshopUsers;
use Tests\TestCase;

class DiagnosticTest extends TestCase
{
    use CreatesWorkshopUsers, RefreshDatabase;

    private function answers(array $byAxis): array
    {
        $answers = [];
        foreach (app(WorkshopContent::class)->statements() as $index => $statement) {
            $answers[$index] = $byAxis[$statement['axis']];
        }

        return $answers;
    }

    public function test_le_formulaire_affiche_les_24_affirmations(): void
    {
        $user = $this->participant();

        $response = $this->actingAs($user)->get('/3x30/diagnostic');

        $response->assertOk();
        foreach (app(WorkshopContent::class)->statements() as $statement) {
            $response->assertSee($statement['text']);
        }
    }

    public function test_un_diagnostic_complet_enregistre_les_scores_et_l_axe_phare(): void
    {
        $user = $this->participant();

        $this->actingAs($user)->post('/3x30/diagnostic', ['answers' => $this->answers(['filiation' => 4, 'desert' => 1, 'appel' => 2])])
            ->assertRedirect(route('workshop.diagnostic.result'));

        $diagnostic = Diagnostic::first();
        $this->assertSame(100, $diagnostic->score_filiation);
        $this->assertSame(0, $diagnostic->score_desert);
        $this->assertSame('filiation', $diagnostic->axis);

        $this->actingAs($user)->get('/3x30/diagnostic/resultat')->assertOk()->assertSee('axe phare');
    }

    public function test_une_reponse_manquante_est_refusee(): void
    {
        $user = $this->participant();
        $answers = $this->answers(['filiation' => 2, 'desert' => 2, 'appel' => 2]);
        unset($answers[3]);

        $this->actingAs($user)->post('/3x30/diagnostic', ['answers' => $answers])->assertSessionHasErrors();

        $this->assertDatabaseCount('diagnostics', 0);
    }

    public function test_en_cas_d_egalite_l_utilisateur_choisit_son_axe(): void
    {
        $user = $this->participant();

        $this->actingAs($user)->post('/3x30/diagnostic', ['answers' => $this->answers(['filiation' => 3, 'desert' => 3, 'appel' => 1])]);

        $this->assertNull(Diagnostic::first()->axis);
        $this->actingAs($user)->get('/3x30/diagnostic/resultat')->assertOk()->assertSee('Choisis ton axe phare');

        $this->actingAs($user)->post('/3x30/diagnostic/axe', ['axis' => 'appel'])->assertSessionHasErrors('axis');
        $this->actingAs($user)->post('/3x30/diagnostic/axe', ['axis' => 'desert'])->assertRedirect();

        $this->assertSame('desert', Diagnostic::first()->fresh()->axis);
    }

    public function test_les_autres_axes_restent_consultables(): void
    {
        $user = $this->participant();
        $this->diagnosed($user, 'desert');

        $this->actingAs($user)->get('/3x30/modele/filiation')->assertOk()->assertSee("S'inscrire dans une histoire");
        $this->actingAs($user)->get('/3x30/modele/inconnu')->assertNotFound();
    }
}
