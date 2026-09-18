<?php

namespace Tests\Feature\Live;

use App\Models\LiveAnswer;
use App\Models\LiveQuestion;
use App\Models\LiveSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParticipantLiveTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Les requetes JSON de test n'envoient les cookies qu'avec withCredentials ; le navigateur, lui, les envoie toujours.
        $this->withCredentials();
    }

    private function openSession(string $type = 'choice'): array
    {
        $session = LiveSession::createFromTemplate('reseaux-sociaux');
        $session->activate();
        $question = $session->questions()->where('type', $type)->first();
        $question->update(['status' => LiveQuestion::STATUS_OPEN]);
        $session->update(['current_question_id' => $question->id]);

        return [$session, $question];
    }

    public function test_sans_sondage_en_ligne_la_page_le_dit(): void
    {
        $this->get('/live')->assertOk()->assertSee("Pas d'atelier", false);
        $this->getJson('/live/etat')->assertOk()->assertJson(['session' => null, 'question' => null]);
    }

    public function test_le_modele_reseaux_sociaux_cree_sept_questions(): void
    {
        $session = LiveSession::createFromTemplate('reseaux-sociaux');

        $this->assertCount(7, $session->questions);
        $this->assertSame(['words', 'text', 'choice', 'choice', 'words', 'text', 'choice'], $session->questions->pluck('type')->all());
        $this->assertCount(4, $session->questions[3]->options);
    }

    public function test_un_participant_repond_une_seule_fois_par_question_et_peut_modifier(): void
    {
        [$session, $question] = $this->openSession('choice');

        $this->withCookie('live_token', str_repeat('a', 40))
            ->postJson('/live/repondre', ['question_id' => $question->id, 'option_index' => 0])
            ->assertOk()->assertJson(['ok' => true]);

        $this->withCookie('live_token', str_repeat('a', 40))
            ->postJson('/live/repondre', ['question_id' => $question->id, 'option_index' => 2])
            ->assertOk();

        $this->withCookie('live_token', str_repeat('b', 40))
            ->postJson('/live/repondre', ['question_id' => $question->id, 'option_index' => 2])
            ->assertOk();

        $this->assertSame(2, LiveAnswer::count());
        $this->assertSame(2, LiveAnswer::where('token', str_repeat('a', 40))->value('option_index'));

        $state = $this->withCookie('live_token', str_repeat('a', 40))->getJson('/live/etat');
        $state->assertOk()->assertJsonPath('answer.option_index', 2)->assertJsonPath('results', null);
    }

    public function test_un_premier_visiteur_recoit_un_jeton_en_cookie(): void
    {
        $this->openSession();

        $this->get('/live')->assertOk()->assertCookie('live_token');
    }

    public function test_une_question_fermee_refuse_les_reponses(): void
    {
        [$session, $question] = $this->openSession('choice');
        $question->update(['status' => LiveQuestion::STATUS_CLOSED]);

        $this->withCookie('live_token', str_repeat('a', 40))
            ->postJson('/live/repondre', ['question_id' => $question->id, 'option_index' => 0])
            ->assertStatus(409);

        $this->assertSame(0, LiveAnswer::count());
    }

    public function test_une_option_inexistante_est_refusee(): void
    {
        [$session, $question] = $this->openSession('choice');

        $this->withCookie('live_token', str_repeat('a', 40))
            ->postJson('/live/repondre', ['question_id' => $question->id, 'option_index' => 9])
            ->assertStatus(422);
    }

    public function test_les_mots_sont_nettoyes_et_agreges(): void
    {
        [$session, $question] = $this->openSession('words');

        foreach (['  Instagram ', 'instagram', 'La société', 'INSTAGRAM'] as $i => $word) {
            $this->withCookie('live_token', str_pad((string) $i, 40, 'x'))
                ->postJson('/live/repondre', ['question_id' => $question->id, 'value' => $word])
                ->assertOk();
        }

        $this->withCookie('live_token', str_pad('9', 40, 'x'))
            ->postJson('/live/repondre', ['question_id' => $question->id, 'value' => '   '])
            ->assertStatus(422);

        $results = $question->fresh()->results();
        $this->assertSame(4, $results['total']);
        $this->assertSame(['word' => 'instagram', 'count' => 3], $results['items'][0]);
        $this->assertSame(['word' => 'la société', 'count' => 1], $results['items'][1]);
    }

    public function test_les_resultats_apparaissent_quand_l_animateur_les_affiche(): void
    {
        [$session, $question] = $this->openSession('choice');
        $this->withCookie('live_token', str_repeat('a', 40))
            ->postJson('/live/repondre', ['question_id' => $question->id, 'option_index' => 1])->assertOk();

        $this->getJson('/live/ecran/etat')->assertOk()->assertJsonPath('count', 1)->assertJsonPath('results', null);

        $question->update(['show_results' => true]);

        $this->getJson('/live/ecran/etat')->assertOk()
            ->assertJsonPath('results.total', 1)
            ->assertJsonPath('results.items.1.count', 1)
            ->assertJsonPath('results.items.1.pct', 100)
            ->assertJsonPath('session.join_url', rtrim(config('app.url'), '/').'/live');

        $this->getJson('/live/etat')->assertOk()->assertJsonPath('results.total', 1);
    }

    public function test_le_texte_libre_est_limite_et_liste_du_plus_recent_au_plus_ancien(): void
    {
        [$session, $question] = $this->openSession('text');

        $this->withCookie('live_token', str_repeat('a', 40))
            ->postJson('/live/repondre', ['question_id' => $question->id, 'value' => str_repeat('b', 500)])
            ->assertStatus(422);

        $this->withCookie('live_token', str_repeat('a', 40))
            ->postJson('/live/repondre', ['question_id' => $question->id, 'value' => "Un vieux banc  usé\n par des mains"])
            ->assertOk();

        $this->assertSame('Un vieux banc usé par des mains', LiveAnswer::first()->value);
    }
}
