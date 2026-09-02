<?php

namespace Tests\Feature\Workshop;

use App\Models\Anchor;
use App\Services\Workshop\WorkshopClock;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesWorkshopUsers;
use Tests\TestCase;

class AnchorTest extends TestCase
{
    use CreatesWorkshopUsers, RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    public function test_sans_diagnostic_on_est_renvoye_vers_le_diagnostic(): void
    {
        $user = $this->participant();

        $this->actingAs($user)->get('/3x30/ancrage/nouveau')->assertRedirect(route('workshop.diagnostic'));
    }

    public function test_poser_un_geste_clot_le_precedent(): void
    {
        $user = $this->participant();
        $this->diagnosed($user);
        $first = $this->anchored($user);

        $this->actingAs($user)->post('/3x30/ancrage', [
            'axis' => 'desert',
            'manquement' => 'Je contrôle tout.',
            'gesture' => 'Une phrase chaque soir.',
            'confidant' => 'Flo',
        ])->assertRedirect(route('workshop.index'));

        $this->assertFalse($first->fresh()->is_active);
        $this->assertNotNull($first->fresh()->ended_on);
        $this->assertSame(1, Anchor::where('is_active', true)->count());
        $this->assertSame('Une phrase chaque soir.', $user->activeAnchor()->gesture);
    }

    public function test_le_confident_est_obligatoire(): void
    {
        $user = $this->participant();
        $this->diagnosed($user);

        $this->actingAs($user)->post('/3x30/ancrage', [
            'axis' => 'desert',
            'manquement' => 'x',
            'gesture' => 'y',
            'confidant' => '',
        ])->assertSessionHasErrors('confidant');
    }

    public function test_le_pointage_accepte_aujourd_hui_et_hier_seulement(): void
    {
        CarbonImmutable::setTestNow('2026-09-10 12:00:00');
        $user = $this->participant();
        $this->diagnosed($user);
        $anchor = $this->anchored($user, ['started_on' => '2026-09-01']);

        $today = WorkshopClock::today()->toDateString();
        $yesterday = WorkshopClock::yesterday()->toDateString();

        $this->actingAs($user)->post('/3x30/ancrage/pointage', ['day' => $today, 'held' => 1])->assertRedirect();
        $this->actingAs($user)->post('/3x30/ancrage/pointage', ['day' => $yesterday, 'held' => 0])->assertRedirect();
        $this->actingAs($user)->post('/3x30/ancrage/pointage', ['day' => '2026-09-05', 'held' => 1])->assertSessionHasErrors('day');

        $this->assertSame(2, $anchor->checkins()->count());
        $this->assertTrue($anchor->checkinFor(WorkshopClock::today())->held);
        $this->assertFalse($anchor->checkinFor(WorkshopClock::yesterday())->held);
    }

    public function test_un_seul_pointage_par_jour_la_seconde_saisie_remplace(): void
    {
        $user = $this->participant();
        $this->diagnosed($user);
        $anchor = $this->anchored($user);
        $today = WorkshopClock::today()->toDateString();

        $this->actingAs($user)->post('/3x30/ancrage/pointage', ['day' => $today, 'held' => 0]);
        $this->actingAs($user)->post('/3x30/ancrage/pointage', ['day' => $today, 'held' => 1]);

        $this->assertSame(1, $anchor->checkins()->count());
        $this->assertTrue($anchor->checkins()->first()->held);
    }

    public function test_le_tableau_de_bord_n_affiche_ni_serie_ni_pourcentage(): void
    {
        $user = $this->participant();
        $this->diagnosed($user);
        $this->anchored($user);

        $response = $this->actingAs($user)->get('/3x30');

        $response->assertOk()
            ->assertSee('Cinq minutes sans écran.')
            ->assertDontSee('streak')
            ->assertDontSee('jours d\'affilée')
            ->assertDontSee('/ 100');
    }
}
