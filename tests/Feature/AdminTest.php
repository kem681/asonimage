<?php

namespace Tests\Feature;

use App\Models\AuthorizedEmail;
use App\Models\Edition;
use App\Models\Resource;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_utilisateur_non_admin_ne_peut_pas_acceder_aux_routes_admin(): void
    {
        $user = User::factory()->create(['is_admin' => false]);

        $this->actingAs($user)->get('/admin/emails-autorises')->assertForbidden();
        $this->actingAs($user)->get('/admin/ressources')->assertForbidden();
    }

    public function test_un_admin_peut_ajouter_un_email_manuellement(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $edition = Edition::create(['year' => 2026, 'label' => 'Edition 2026']);

        $response = $this->actingAs($admin)->post('/admin/emails-autorises', [
            'email' => 'nouveau@example.com',
            'name' => 'Nouveau',
            'edition_id' => $edition->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('authorized_emails', [
            'email' => 'nouveau@example.com',
            'source' => AuthorizedEmail::SOURCE_ADMIN_MANUEL,
        ]);
    }

    public function test_un_admin_peut_importer_un_csv(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $edition = Edition::create(['year' => 2026, 'label' => 'Edition 2026']);

        $csv = "Participant,Mail,Telephone\n"
            ."Jean Dupont,jean.dupont@example.com,0600000000\n"
            ."Sans Email,,0600000001\n"
            ."Mauvais Email,pas-un-email,0600000002\n";

        $file = UploadedFile::fake()->createWithContent('inscrits.csv', $csv);

        $response = $this->actingAs($admin)->post('/admin/emails-autorises/import', [
            'edition_id' => $edition->id,
            'csv' => $file,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('authorized_emails', ['email' => 'jean.dupont@example.com']);
        $this->assertDatabaseCount('authorized_emails', 1);
    }

    public function test_un_admin_peut_publier_une_ressource_et_un_membre_peut_la_telecharger(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['is_admin' => true]);
        $edition = Edition::create(['year' => 2026, 'label' => 'Edition 2026']);

        $file = UploadedFile::fake()->create('plan-jour-1.pptx', 500);

        $store = $this->actingAs($admin)->post('/admin/ressources', [
            'title' => 'Plan detaille jour 1',
            'description' => 'Le plan du jour 1',
            'edition_id' => $edition->id,
            'day' => 1,
            'file' => $file,
        ]);

        $store->assertRedirect(route('admin.resources.index'));
        $resource = Resource::firstOrFail();
        Storage::disk('public')->assertExists($resource->file_path);

        $member = User::factory()->create(['is_admin' => false]);
        $download = $this->actingAs($member)->get(route('membres.ressources.telecharger', $resource));

        $download->assertOk();
    }
}
