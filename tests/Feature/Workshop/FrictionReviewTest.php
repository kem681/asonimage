<?php

namespace Tests\Feature\Workshop;

use App\Models\Review;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesWorkshopUsers;
use Tests\TestCase;

class FrictionReviewTest extends TestCase
{
    use CreatesWorkshopUsers, RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    public function test_une_seule_entree_de_frottement_par_semaine(): void
    {
        CarbonImmutable::setTestNow('2026-09-09 10:00:00'); // mercredi
        $user = $this->participant();
        $this->diagnosed($user);
        $anchor = $this->anchored($user, ['started_on' => '2026-09-01']);

        $this->actingAs($user)->post('/3x30/frottement', ['body' => 'Le téléphone au lit.'])->assertRedirect();
        $this->actingAs($user)->post('/3x30/frottement', ['body' => 'Le téléphone au lit, encore.', 'told_to' => 'Marc', 'told' => 1])->assertRedirect();

        $this->assertSame(1, $anchor->frictions()->count());
        $friction = $anchor->frictions()->first();
        $this->assertSame('2026-09-07', $friction->week_start->toDateString());
        $this->assertSame('Le téléphone au lit, encore.', $friction->body);
        $this->assertTrue($friction->isTold());

        CarbonImmutable::setTestNow('2026-09-15 10:00:00'); // semaine suivante
        $this->actingAs($user)->post('/3x30/frottement', ['body' => 'Autre chose.']);
        $this->assertSame(2, $anchor->frictions()->count());
    }

    public function test_un_frottement_peut_etre_marque_dit_plus_tard(): void
    {
        $user = $this->participant();
        $this->diagnosed($user);
        $anchor = $this->anchored($user);

        $this->actingAs($user)->post('/3x30/frottement', ['body' => 'x']);
        $friction = $anchor->frictions()->first();
        $this->assertFalse($friction->isTold());

        $this->actingAs($user)->post("/3x30/frottement/{$friction->id}/dit", ['told_to' => 'Flo'])->assertRedirect();

        $this->assertTrue($friction->fresh()->isTold());
        $this->assertSame('Flo', $friction->fresh()->told_to);
    }

    public function test_la_revue_est_refusee_avant_28_jours_et_acceptee_apres(): void
    {
        $user = $this->participant();
        $this->diagnosed($user);
        $anchor = $this->anchored($user, ['started_on' => '2026-09-01']);

        CarbonImmutable::setTestNow('2026-09-20 10:00:00');
        $this->actingAs($user)->get('/3x30/fidelite')->assertRedirect(route('workshop.path'));
        $this->actingAs($user)->post('/3x30/fidelite', ['held' => 'oui', 'changed' => 'x', 'decision' => 'continuer'])->assertRedirect(route('workshop.path'));
        $this->assertDatabaseCount('reviews', 0);

        CarbonImmutable::setTestNow('2026-09-29 10:00:00');
        $this->actingAs($user)->get('/3x30/fidelite')->assertOk();
        $this->actingAs($user)->post('/3x30/fidelite', ['held' => 'partie', 'changed' => 'Un peu plus de calme.', 'decision' => 'continuer'])
            ->assertRedirect(route('workshop.memorial'));

        $review = Review::first();
        $this->assertSame($anchor->id, $review->anchor_id);
        $this->assertSame('partie', $review->held);
        $this->assertSame('2026-09-29', $review->reviewed_on->toDateString());
        $this->assertTrue($anchor->fresh()->is_active);
        $this->assertSame('2026-10-27', $anchor->fresh()->reviewDueOn()->toDateString());
    }

    public function test_choisir_un_nouveau_geste_clot_l_ancrage(): void
    {
        $user = $this->participant();
        $this->diagnosed($user);
        $anchor = $this->anchored($user, ['started_on' => '2026-08-01']);
        CarbonImmutable::setTestNow('2026-09-10 10:00:00');

        $this->actingAs($user)->post('/3x30/fidelite', ['held' => 'non', 'changed' => 'Rien.', 'decision' => Review::DECISION_NEW_GESTURE])
            ->assertRedirect(route('workshop.anchor.create', ['axe' => 'desert']));

        $this->assertFalse($anchor->fresh()->is_active);
        $this->assertNull($user->activeAnchor());
    }

    public function test_refaire_le_diagnostic_clot_l_ancrage(): void
    {
        $user = $this->participant();
        $this->diagnosed($user);
        $anchor = $this->anchored($user, ['started_on' => '2026-08-01']);
        CarbonImmutable::setTestNow('2026-09-10 10:00:00');

        $this->actingAs($user)->post('/3x30/fidelite', ['held' => 'oui', 'changed' => 'x', 'decision' => Review::DECISION_DIAGNOSTIC])
            ->assertRedirect(route('workshop.diagnostic'));

        $this->assertFalse($anchor->fresh()->is_active);
    }

    public function test_le_memorial_liste_les_revues_dans_l_ordre(): void
    {
        $user = $this->participant();
        $this->diagnosed($user);
        $anchor = $this->anchored($user, ['started_on' => '2026-06-01']);
        Review::create(['anchor_id' => $anchor->id, 'held' => 'oui', 'changed' => 'Première pierre.', 'reviewed_on' => '2026-06-29']);
        Review::create(['anchor_id' => $anchor->id, 'held' => 'partie', 'changed' => 'Deuxième pierre.', 'reviewed_on' => '2026-07-27']);

        $this->actingAs($user)->get('/3x30/memorial')->assertOk()->assertSeeInOrder(['Première pierre.', 'Deuxième pierre.']);
    }
}
