<?php

namespace Tests\Feature\Live;

use App\Models\LiveAnswer;
use App\Models\LiveQuestion;
use App\Models\LiveSession;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminLiveTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true]);
    }

    public function test_seul_un_admin_pilote_les_sondages(): void
    {
        $this->get('/sondage')->assertRedirect('/connexion');
        $this->actingAs(User::factory()->create(['is_admin' => false]))->get('/sondage')->assertForbidden();
        $this->actingAs($this->admin())->get('/sondage')->assertOk()->assertSee('Réseaux sociaux : comment gérer pour grandir ?');
    }

    public function test_un_admin_cree_un_sondage_depuis_le_modele_et_le_met_en_ligne(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)->post('/sondage', ['template' => 'reseaux-sociaux'])->assertRedirect();

        $session = LiveSession::first();
        $this->assertSame(4, strlen($session->code));
        $this->assertFalse($session->is_active);
        $this->assertCount(13, $session->questions);

        $this->actingAs($admin)->post("/sondage/{$session->id}/activer")->assertRedirect();
        $this->assertTrue($session->fresh()->is_active);

        // Une seconde session activee desactive la premiere.
        $other = LiveSession::createFromTemplate('reseaux-sociaux');
        $this->actingAs($admin)->post("/sondage/{$other->id}/activer")->assertRedirect();
        $this->assertFalse($session->fresh()->is_active);
        $this->assertTrue($other->fresh()->is_active);
        $this->assertSame($other->id, LiveSession::active()->id);
    }

    public function test_ouvrir_une_question_ferme_la_precedente_et_la_rend_courante(): void
    {
        $admin = $this->admin();
        $session = LiveSession::createFromTemplate('reseaux-sociaux');
        [$q1, $q2] = [$session->questions[0], $session->questions[1]];

        $this->actingAs($admin)->post("/sondage/{$session->id}/questions/{$q1->id}/ouvrir")->assertRedirect();
        $this->assertSame(LiveQuestion::STATUS_OPEN, $q1->fresh()->status);
        $this->assertSame($q1->id, $session->fresh()->current_question_id);

        $this->actingAs($admin)->post("/sondage/{$session->id}/questions/{$q2->id}/ouvrir")->assertRedirect();
        $this->assertSame(LiveQuestion::STATUS_CLOSED, $q1->fresh()->status);
        $this->assertSame(LiveQuestion::STATUS_OPEN, $q2->fresh()->status);
        $this->assertSame($q2->id, $session->fresh()->current_question_id);

        $this->actingAs($admin)->post("/sondage/{$session->id}/questions/{$q2->id}/resultats")->assertRedirect();
        $this->assertTrue($q2->fresh()->show_results);

        $this->actingAs($admin)->post("/sondage/{$session->id}/attente")->assertRedirect();
        $this->assertNull($session->fresh()->current_question_id);
    }

    public function test_la_page_de_pilotage_affiche_les_questions_et_les_boutons(): void
    {
        $admin = $this->admin();
        $session = LiveSession::createFromTemplate('reseaux-sociaux');
        $session->questions[0]->update(['status' => LiveQuestion::STATUS_OPEN, 'show_results' => true]);

        $this->actingAs($admin)->get("/sondage/{$session->id}")->assertOk()
            ->assertSee('qui fixe les critères de beauté')
            ->assertSee('Lequel des deux visages est beau')
            ->assertSee('Ma décision pour cette semaine')
            ->assertSee('Mettre en ligne')
            ->assertSee('Fermer')
            ->assertSee('Masquer les résultats')
            ->assertSee('Export CSV');

        $this->actingAs($admin)->getJson("/sondage/{$session->id}/etat")->assertOk()
            ->assertJsonPath('questions.0.status', 'open')
            ->assertJsonPath('questions.0.answers', 0);

        $this->get('/live/ecran')->assertOk()->assertSee("Pas d'atelier", false);
    }

    public function test_la_vue_presentateur_montre_les_notes_et_les_resultats_avant_la_salle(): void
    {
        $admin = $this->admin();
        $session = LiveSession::createFromTemplate('reseaux-sociaux');
        $q1 = $session->questions[0];

        // Ecran d'attente : bouton pour ouvrir Q1 et ses notes.
        $this->actingAs($admin)->get("/sondage/{$session->id}/presenter")->assertOk()
            ->assertSee('Ouvrir Q1')->assertSee('Personne n\'a écrit');

        $q1->update(['status' => LiveQuestion::STATUS_OPEN]);
        $session->update(['current_question_id' => $q1->id]);
        LiveAnswer::create(['live_question_id' => $q1->id, 'token' => str_repeat('a', 40), 'value' => 'instagram']);

        $this->actingAs($admin)->get("/sondage/{$session->id}/presenter")->assertOk()
            ->assertSee('Question 1 sur 13')->assertSee('Suivante : ouvrir Q2')->assertSee('Mes notes')->assertSee('Fermer');

        // L'animateur voit les resultats meme si la salle ne les voit pas encore.
        $this->actingAs($admin)->getJson("/sondage/{$session->id}/etat")->assertOk()
            ->assertJsonPath('current.id', $q1->id)
            ->assertJsonPath('current.show_results', false)
            ->assertJsonPath('current.results.items.0.word', 'instagram');
        $this->getJson('/live/etat')->assertJsonPath('results', null);
    }

    public function test_une_question_d_un_autre_sondage_est_introuvable(): void
    {
        $admin = $this->admin();
        $a = LiveSession::createFromTemplate('reseaux-sociaux');
        $b = LiveSession::createFromTemplate('reseaux-sociaux');

        $this->actingAs($admin)->post("/sondage/{$a->id}/questions/{$b->questions[0]->id}/ouvrir")->assertNotFound();
    }

    public function test_effacer_supprime_les_reponses_et_remet_en_attente(): void
    {
        $admin = $this->admin();
        $session = LiveSession::createFromTemplate('reseaux-sociaux');
        $q = $session->questions[2];
        $q->update(['status' => LiveQuestion::STATUS_OPEN, 'show_results' => true]);
        LiveAnswer::create(['live_question_id' => $q->id, 'token' => str_repeat('a', 40), 'option_index' => 0]);

        $this->actingAs($admin)->post("/sondage/{$session->id}/questions/{$q->id}/effacer")->assertRedirect();

        $this->assertSame(0, $q->answers()->count());
        $this->assertSame(LiveQuestion::STATUS_PENDING, $q->fresh()->status);
        $this->assertFalse($q->fresh()->show_results);
    }

    public function test_ajout_d_une_question_a_choix_exige_deux_options(): void
    {
        $admin = $this->admin();
        $session = LiveSession::create(['code' => 'TEST', 'title' => 'Vide']);

        $this->actingAs($admin)->post("/sondage/{$session->id}/questions", ['type' => 'choice', 'prompt' => 'Alors ?', 'options' => "Oui"])
            ->assertSessionHasErrors('options');

        $this->actingAs($admin)->post("/sondage/{$session->id}/questions", ['type' => 'choice', 'prompt' => 'Alors ?', 'options' => "Oui\n\nNon \n"])
            ->assertRedirect();

        $q = $session->questions()->first();
        $this->assertSame(['Oui', 'Non'], $q->options);
        $this->assertSame(1, $q->position);
    }

    public function test_l_export_csv_contient_les_reponses_en_clair(): void
    {
        $admin = $this->admin();
        $session = LiveSession::createFromTemplate('reseaux-sociaux');
        $choice = $session->questions[1];
        $words = $session->questions[0];
        LiveAnswer::create(['live_question_id' => $choice->id, 'token' => str_repeat('a', 40), 'option_index' => 1]);
        LiveAnswer::create(['live_question_id' => $words->id, 'token' => str_repeat('a', 40), 'value' => 'instagram']);

        $response = $this->actingAs($admin)->get("/sondage/{$session->id}/export");

        $response->assertOk();
        $csv = $response->streamedContent();
        $this->assertStringContainsString('Celui de droite', $csv);
        $this->assertStringContainsString('instagram', $csv);
        $this->assertStringContainsString('Nuage de mots', $csv);
    }

    public function test_supprimer_le_sondage_efface_questions_et_reponses(): void
    {
        $admin = $this->admin();
        $session = LiveSession::createFromTemplate('reseaux-sociaux');
        LiveAnswer::create(['live_question_id' => $session->questions[0]->id, 'token' => str_repeat('a', 40), 'value' => 'x']);

        $this->actingAs($admin)->delete("/sondage/{$session->id}")->assertRedirect('/sondage');

        $this->assertSame(0, LiveSession::count());
        $this->assertSame(0, LiveQuestion::count());
        $this->assertSame(0, LiveAnswer::count());
    }
}
