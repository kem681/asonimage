<?php

namespace Tests\Feature\Workshop;

use App\Models\Friction;
use App\Models\Review;
use App\Models\User;
use App\Models\WorkshopGroup;
use App\Services\Workshop\WorkshopClock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesWorkshopUsers;
use Tests\TestCase;

class PrivacyTest extends TestCase
{
    use CreatesWorkshopUsers, RefreshDatabase;

    public function test_un_utilisateur_ne_peut_pas_toucher_au_frottement_d_un_autre(): void
    {
        $owner = $this->participant();
        $this->diagnosed($owner);
        $anchor = $this->anchored($owner);
        $friction = Friction::create(['anchor_id' => $anchor->id, 'week_start' => '2026-09-07', 'body' => 'Secret.']);

        $other = $this->participant();

        $this->actingAs($other)->post("/3x30/frottement/{$friction->id}/dit", ['told_to' => 'X'])->assertNotFound();
        $this->assertFalse($friction->fresh()->isTold());
    }

    public function test_un_participant_ne_voit_jamais_le_contenu_d_un_autre_sur_son_chemin(): void
    {
        $owner = $this->participant();
        $this->diagnosed($owner);
        $this->anchored($owner, ['gesture' => 'Geste très privé.']);

        $other = $this->participant();
        $this->diagnosed($other);
        $this->anchored($other, ['gesture' => 'Mon geste à moi.']);

        $this->actingAs($other)->get('/3x30/chemin')->assertOk()->assertSee('Mon geste à moi.')->assertDontSee('Geste très privé.');
        $this->actingAs($other)->get('/3x30')->assertOk()->assertDontSee('Geste très privé.');
    }

    public function test_aucune_page_admin_ne_montre_le_contenu_ecrit(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $owner = $this->participant();
        $this->diagnosed($owner);
        $anchor = $this->anchored($owner, ['manquement' => 'MANQUEMENT-PRIVE', 'gesture' => 'GESTE-PRIVE']);
        Friction::create(['anchor_id' => $anchor->id, 'week_start' => '2026-09-07', 'body' => 'FROTTEMENT-PRIVE']);
        Review::create(['anchor_id' => $anchor->id, 'held' => 'oui', 'changed' => 'REVUE-PRIVEE', 'reviewed_on' => '2026-09-29']);

        foreach (['/admin/3x30/codes', '/admin/3x30/participants', '/admin/3x30/chiffres'] as $url) {
            $this->actingAs($admin)->get($url)->assertOk()
                ->assertDontSee('MANQUEMENT-PRIVE')
                ->assertDontSee('GESTE-PRIVE')
                ->assertDontSee('FROTTEMENT-PRIVE')
                ->assertDontSee('REVUE-PRIVEE');
        }

        $this->actingAs($admin)->get('/admin/3x30/participants/export')->assertOk()->assertDontSee('GESTE-PRIVE');
    }

    public function test_la_suppression_du_compte_efface_tout(): void
    {
        $user = $this->participant();
        $this->diagnosed($user);
        $anchor = $this->anchored($user);
        $this->actingAs($user)->post('/3x30/ancrage/pointage', ['day' => WorkshopClock::today()->toDateString(), 'held' => 1]);
        Friction::create(['anchor_id' => $anchor->id, 'week_start' => '2026-09-07', 'body' => 'x']);
        Review::create(['anchor_id' => $anchor->id, 'held' => 'oui', 'changed' => 'x', 'reviewed_on' => '2026-09-29']);
        $this->actingAs($user)->post('/3x30/groupe', ['name' => 'Seul']);

        $this->actingAs($user)->delete('/3x30/profil', ['confirmation' => 'SUPPRIMER'])->assertRedirect(route('landing'));

        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('workshop_participants', 0);
        $this->assertDatabaseCount('diagnostics', 0);
        $this->assertDatabaseCount('anchors', 0);
        $this->assertDatabaseCount('anchor_checkins', 0);
        $this->assertDatabaseCount('frictions', 0);
        $this->assertDatabaseCount('reviews', 0);
        $this->assertDatabaseCount('workshop_group_members', 0);
        $this->assertSame(0, WorkshopGroup::count());
    }

    public function test_la_suppression_exige_le_mot_de_confirmation(): void
    {
        $user = $this->participant();

        $this->actingAs($user)->delete('/3x30/profil', ['confirmation' => 'oui'])->assertSessionHasErrors('confirmation');

        $this->assertDatabaseCount('users', 1);
    }

    public function test_un_non_admin_n_accede_pas_aux_pages_admin_3x30(): void
    {
        $user = $this->participant();

        $this->actingAs($user)->get('/admin/3x30/codes')->assertForbidden();
        $this->actingAs($user)->get('/admin/3x30/participants/export')->assertForbidden();
    }
}
