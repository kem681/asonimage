<?php

namespace Tests\Feature\Workshop;

use App\Models\User;
use App\Models\WorkshopCode;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesWorkshopUsers;
use Tests\TestCase;

class AdminWorkshopTest extends TestCase
{
    use CreatesWorkshopUsers, RefreshDatabase;

    public function test_un_admin_cree_et_desactive_un_code(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->post('/admin/3x30/codes', ['code' => '3x30 nov-26', 'label' => 'Atelier novembre'])->assertRedirect();

        $code = WorkshopCode::first();
        // Espaces et tirets sont ignores : "3x30 nov-26" et "3X30NOV26" sont le meme code.
        $this->assertSame('3X30NOV26', $code->code);
        $this->assertTrue($code->is_active);

        $this->actingAs($admin)->post('/admin/3x30/codes', ['code' => '3X30-NOV26', 'label' => 'Doublon'])->assertSessionHasErrors('code');

        $this->actingAs($admin)->post("/admin/3x30/codes/{$code->id}/basculer")->assertRedirect();
        $this->assertFalse($code->fresh()->is_active);
        $this->assertNull(WorkshopCode::findActive('3x30 nov 26'));
    }

    public function test_l_export_csv_contient_les_emails(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $participant = $this->participant(['name' => 'Paul Martin', 'email' => 'paul@example.com']);

        $response = $this->actingAs($admin)->get('/admin/3x30/participants/export');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        $this->assertStringContainsString('paul@example.com', $response->streamedContent());
        $this->assertStringContainsString('Paul Martin', $response->streamedContent());
    }

    public function test_la_page_des_chiffres_compte_les_axes_phares(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $a = $this->participant();
        $this->diagnosed($a, 'desert');
        $b = $this->participant();
        $this->diagnosed($b, 'desert');
        $c = $this->participant();
        $this->diagnosed($c, 'filiation');

        $this->actingAs($admin)->get('/admin/3x30/chiffres')->assertOk()->assertSeeInOrder(['Filiation', '1', 'Désert', '2']);
    }

    public function test_un_admin_accede_a_3x30_sans_code(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);

        $this->actingAs($admin)->get('/3x30')->assertOk();
    }
}
