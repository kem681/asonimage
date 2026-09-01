<?php

namespace Tests\Feature;

use App\Models\AuthorizedEmail;
use App\Models\Edition;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InscriptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_une_inscription_valide_cree_un_email_autorise_et_envoie_les_emails(): void
    {
        Mail::fake();
        Edition::create(['year' => 2026, 'label' => 'Edition 2026']);

        $response = $this->postJson('/inscription', [
            'prenom' => 'Alice',
            'nom' => 'Martin',
            'email' => 'Alice.Martin@Example.com',
            'dob' => '2000-01-01',
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $this->assertDatabaseHas('authorized_emails', [
            'email' => 'alice.martin@example.com',
            'source' => AuthorizedEmail::SOURCE_INSCRIPTION,
        ]);

        Mail::assertSentCount(2);
    }

    public function test_une_inscription_sans_champ_obligatoire_echoue(): void
    {
        $response = $this->postJson('/inscription', [
            'prenom' => 'Alice',
        ]);

        $response->assertStatus(400)->assertJson(['success' => false]);
        $this->assertDatabaseCount('authorized_emails', 0);
    }
}
