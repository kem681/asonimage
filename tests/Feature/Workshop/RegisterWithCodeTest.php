<?php

namespace Tests\Feature\Workshop;

use App\Models\AuthorizedEmail;
use App\Models\Edition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesWorkshopUsers;
use Tests\TestCase;

class RegisterWithCodeTest extends TestCase
{
    use CreatesWorkshopUsers, RefreshDatabase;

    public function test_un_code_valide_cree_le_compte_et_enregistre_le_participant(): void
    {
        $code = $this->workshopCode('LAUSANNE-26');

        $response = $this->post('/creer-un-compte', [
            'name' => 'Paul Martin',
            'email' => 'Paul@Example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'workshop_code' => 'lausanne 26',
        ]);

        $response->assertRedirect(route('workshop.index'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('workshop_participants', [
            'email' => 'paul@example.com',
            'workshop_code_id' => $code->id,
        ]);
        $this->assertNull(User::first()->edition_id);
    }

    public function test_un_code_inactif_ou_inconnu_refuse_la_creation(): void
    {
        $this->workshopCode('FERME', active: false);

        foreach (['FERME', 'INCONNU'] as $code) {
            $response = $this->post('/creer-un-compte', [
                'name' => 'Paul',
                'email' => 'paul@example.com',
                'password' => 'password123',
                'password_confirmation' => 'password123',
                'workshop_code' => $code,
            ]);

            $response->assertSessionHasErrors('workshop_code');
        }

        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_un_email_autorise_et_un_code_donnent_les_deux_acces(): void
    {
        $edition = Edition::create(['year' => 2026, 'label' => 'Edition 2026']);
        AuthorizedEmail::create(['email' => 'bob@example.com', 'edition_id' => $edition->id, 'source' => AuthorizedEmail::SOURCE_INSCRIPTION]);
        $this->workshopCode('CODE1');

        $this->post('/creer-un-compte', [
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'workshop_code' => 'CODE1',
        ])->assertRedirect(route('workshop.index'));

        $user = User::first();
        $this->assertSame($edition->id, $user->edition_id);
        $this->assertTrue($user->isWorkshopParticipant());
    }

    public function test_sans_code_le_flux_seminaire_reste_inchange(): void
    {
        $this->post('/creer-un-compte', [
            'name' => 'Bob',
            'email' => 'inconnu@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertDatabaseCount('users', 0);
    }

    public function test_un_membre_connecte_peut_entrer_avec_un_code(): void
    {
        $code = $this->workshopCode('APRES');
        $user = User::factory()->create();

        $this->actingAs($user)->get('/3x30')->assertRedirect(route('workshop.code'));

        $this->actingAs($user)->post('/3x30/code', ['workshop_code' => 'apres'])->assertRedirect(route('workshop.index'));

        $this->assertDatabaseHas('workshop_participants', ['user_id' => $user->id, 'workshop_code_id' => $code->id]);
        $this->actingAs($user)->get('/3x30')->assertOk();
    }

    public function test_la_page_rejoindre_prefille_le_code(): void
    {
        $this->get('/3x30/rejoindre?code=ABC')->assertOk()->assertSee('value="ABC"', false);
    }

    public function test_la_connexion_envoie_un_participant_pur_vers_3x30(): void
    {
        $user = $this->participant(['password' => bcrypt('password123')]);

        $this->post('/connexion', ['email' => $user->email, 'password' => 'password123'])
            ->assertRedirect(route('workshop.index'));
    }
}
