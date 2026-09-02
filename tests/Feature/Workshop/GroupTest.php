<?php

namespace Tests\Feature\Workshop;

use App\Models\WorkshopGroup;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesWorkshopUsers;
use Tests\TestCase;

class GroupTest extends TestCase
{
    use CreatesWorkshopUsers, RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    public function test_creer_un_groupe_donne_un_code_et_inscrit_le_createur(): void
    {
        $user = $this->participant();

        $this->actingAs($user)->post('/3x30/groupe', ['name' => 'Les gars du jeudi'])->assertRedirect();

        $group = WorkshopGroup::first();
        $this->assertSame(6, strlen($group->code));
        $this->assertMatchesRegularExpression('/^[ABCDEFGHJKMNPQRSTUVWXYZ23456789]{6}$/', $group->code);
        $this->assertTrue($group->hasMember($user));
    }

    public function test_rejoindre_par_code_et_refuser_le_treizieme(): void
    {
        $creator = $this->participant();
        $this->actingAs($creator)->post('/3x30/groupe', ['name' => 'G']);
        $group = WorkshopGroup::first();

        for ($i = 0; $i < 11; $i++) {
            $member = $this->participant();
            $this->actingAs($member)->post('/3x30/groupe/rejoindre', ['code' => strtolower($group->code)])->assertRedirect(route('workshop.group.show', $group));
        }
        $this->assertSame(12, $group->members()->count());

        $thirteenth = $this->participant();
        $this->actingAs($thirteenth)->post('/3x30/groupe/rejoindre', ['code' => $group->code])->assertSessionHasErrors('code');
        $this->assertSame(12, $group->members()->count());

        $this->actingAs($thirteenth)->post('/3x30/groupe/rejoindre', ['code' => 'ZZZZZZ'])->assertSessionHasErrors('code');
    }

    public function test_un_non_membre_ne_voit_pas_le_groupe(): void
    {
        $creator = $this->participant();
        $this->actingAs($creator)->post('/3x30/groupe', ['name' => 'G']);
        $group = WorkshopGroup::first();

        $other = $this->participant();
        $this->actingAs($other)->get("/3x30/groupe/{$group->id}")->assertNotFound();
        $this->actingAs($other)->post("/3x30/groupe/{$group->id}/contact")->assertNotFound();
    }

    public function test_on_s_est_parle_met_a_jour_la_date_pour_tous_et_leve_le_silence(): void
    {
        CarbonImmutable::setTestNow('2026-09-01 10:00:00');
        $creator = $this->participant();
        $this->actingAs($creator)->post('/3x30/groupe', ['name' => 'G']);
        $group = WorkshopGroup::first();
        $member = $this->participant();
        $this->actingAs($member)->post('/3x30/groupe/rejoindre', ['code' => $group->code]);

        CarbonImmutable::setTestNow('2026-09-25 10:00:00');
        $this->assertTrue($group->fresh()->isSilent());

        $this->actingAs($member)->post("/3x30/groupe/{$group->id}/contact")->assertRedirect();

        $this->assertFalse($group->fresh()->isSilent());
        $this->assertSame(0, $group->fresh()->daysSinceLastContact());
        $this->actingAs($creator)->get("/3x30/groupe/{$group->id}")->assertOk()->assertSee('Il y a 0 jour');
    }

    public function test_quitter_un_groupe_vide_le_supprime(): void
    {
        $creator = $this->participant();
        $this->actingAs($creator)->post('/3x30/groupe', ['name' => 'G']);
        $group = WorkshopGroup::first();

        $this->actingAs($creator)->post("/3x30/groupe/{$group->id}/quitter")->assertRedirect(route('workshop.group.index'));

        $this->assertDatabaseCount('workshop_groups', 0);
    }

    public function test_le_groupe_ne_montre_que_les_prenoms(): void
    {
        $creator = $this->participant(['name' => 'Jonathan Saber']);
        $this->actingAs($creator)->post('/3x30/groupe', ['name' => 'G']);
        $group = WorkshopGroup::first();

        $this->actingAs($creator)->get("/3x30/groupe/{$group->id}")->assertOk()->assertSee('Jonathan')->assertDontSee('Saber');
    }
}
