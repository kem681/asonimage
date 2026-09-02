<?php

namespace Tests\Feature\Workshop;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkshopHostTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_racine_du_sous_domaine_app_redirige_vers_3x30(): void
    {
        config(['app.workshop_hosts' => 'app.asonimage.ch']);

        $this->get('http://app.asonimage.ch/')->assertRedirect('/3x30');
        $this->get('http://app.asonimage.ch/connexion')->assertOk();
    }

    public function test_la_racine_du_domaine_principal_garde_la_landing_page(): void
    {
        config(['app.workshop_hosts' => 'app.asonimage.ch']);

        $this->get('http://asonimage.ch/')->assertOk();
    }
}
