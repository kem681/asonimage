<?php

namespace Tests\Feature;

use App\Models\AuthorizedEmail;
use App\Models\Edition;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_creation_de_compte_avec_email_autorise_reussit(): void
    {
        $edition = Edition::create(['year' => 2026, 'label' => 'Edition 2026']);
        AuthorizedEmail::create([
            'email' => 'bob@example.com',
            'name' => 'Bob',
            'edition_id' => $edition->id,
            'source' => AuthorizedEmail::SOURCE_INSCRIPTION,
        ]);

        $response = $this->post('/creer-un-compte', [
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('membres.index'));
        $this->assertDatabaseHas('users', ['email' => 'bob@example.com']);
        $this->assertAuthenticated();
    }

    public function test_creation_de_compte_avec_email_non_autorise_echoue(): void
    {
        $response = $this->post('/creer-un-compte', [
            'name' => 'Bob',
            'email' => 'inconnu@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertDatabaseCount('users', 0);
        $this->assertGuest();
    }

    public function test_connexion_avec_identifiants_valides_ouvre_une_session(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $response = $this->post('/connexion', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('membres.index'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_connexion_avec_mauvais_mot_de_passe_echoue(): void
    {
        $user = User::factory()->create(['password' => bcrypt('password123')]);

        $response = $this->post('/connexion', [
            'email' => $user->email,
            'password' => 'mauvais-mot-de-passe',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_un_visiteur_non_connecte_est_redirige_depuis_membres(): void
    {
        $response = $this->get('/membres');

        $response->assertRedirect(route('login'));
    }
}
