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

    public function test_un_admin_peut_publier_un_pdf_et_un_membre_le_consulte_en_ligne_sans_le_telecharger(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['is_admin' => true]);
        $edition = Edition::create(['year' => 2026, 'label' => 'Edition 2026']);

        $file = UploadedFile::fake()->create('plan-jour-1.pdf', 500, 'application/pdf');

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
        $this->assertFalse($resource->isAudio());

        $member = User::factory()->create(['is_admin' => false]);

        $viewer = $this->actingAs($member)->get(route('membres.ressources.show', $resource));
        $viewer->assertOk();

        $stream = $this->actingAs($member)->get(route('membres.ressources.fichier', $resource));
        $stream->assertOk();
        $stream->assertHeader('content-disposition');
        $this->assertStringStartsWith('inline', $stream->headers->get('content-disposition'));
    }

    public function test_un_admin_peut_publier_un_audio_et_un_membre_peut_l_ecouter(): void
    {
        Storage::fake('public');

        $admin = User::factory()->create(['is_admin' => true]);
        $edition = Edition::create(['year' => 2026, 'label' => 'Edition 2026']);

        $file = UploadedFile::fake()->create('plenieres-jour-2.mp3', 2000, 'audio/mpeg');

        $store = $this->actingAs($admin)->post('/admin/ressources', [
            'title' => 'Plénière jour 2',
            'edition_id' => $edition->id,
            'day' => 2,
            'file' => $file,
        ]);

        $store->assertRedirect(route('admin.resources.index'));
        $resource = Resource::firstOrFail();
        $this->assertTrue($resource->isAudio());

        $member = User::factory()->create(['is_admin' => false]);
        $viewer = $this->actingAs($member)->get(route('membres.ressources.show', $resource));
        $viewer->assertOk();
        $viewer->assertSee('audio', false);
    }

    public function test_un_fichier_powerpoint_est_refuse(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $edition = Edition::create(['year' => 2026, 'label' => 'Edition 2026']);

        $file = UploadedFile::fake()->create('plan.pptx', 500);

        $response = $this->actingAs($admin)->post('/admin/ressources', [
            'title' => 'Plan',
            'edition_id' => $edition->id,
            'day' => 1,
            'file' => $file,
        ]);

        $response->assertSessionHasErrors('file');
        $this->assertDatabaseCount('resources', 0);
    }
}
