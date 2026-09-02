<?php

namespace Tests\Feature\Workshop;

use App\Mail\WorkshopWeeklyReminder;
use App\Models\AnchorCheckin;
use App\Models\Friction;
use App\Models\WorkshopGroup;
use App\Services\Workshop\ReminderResolver;
use App\Services\Workshop\WorkshopClock;
use Carbon\CarbonImmutable;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\Concerns\CreatesWorkshopUsers;
use Tests\TestCase;

class ReminderTest extends TestCase
{
    use CreatesWorkshopUsers, RefreshDatabase;

    protected function tearDown(): void
    {
        CarbonImmutable::setTestNow();
        parent::tearDown();
    }

    private function keys(array $reminders): array
    {
        return array_column($reminders, 'key');
    }

    public function test_sans_diagnostic_un_seul_rappel(): void
    {
        $user = $this->participant();

        $this->assertSame(['diagnostic'], $this->keys(app(ReminderResolver::class)->for($user)));
    }

    public function test_sans_ancrage_le_rappel_est_de_poser_le_geste(): void
    {
        $user = $this->participant();
        $this->diagnosed($user);

        $this->assertSame(['anchor'], $this->keys(app(ReminderResolver::class)->for($user)));
    }

    public function test_les_rappels_du_geste_du_frottement_et_de_la_revue(): void
    {
        CarbonImmutable::setTestNow('2026-09-29 09:00:00'); // mardi, 28 jours apres le 1er
        $user = $this->participant();
        $this->diagnosed($user);
        $anchor = $this->anchored($user, ['started_on' => '2026-09-01']);

        $keys = $this->keys(app(ReminderResolver::class)->for($user));
        $this->assertSame(['checkin', 'friction', 'review'], $keys);

        AnchorCheckin::create(['anchor_id' => $anchor->id, 'day' => WorkshopClock::today()->toDateString(), 'held' => true]);
        Friction::create(['anchor_id' => $anchor->id, 'week_start' => '2026-09-28', 'body' => 'x']);

        $this->assertSame(['review'], $this->keys(app(ReminderResolver::class)->for($user)));

        CarbonImmutable::setTestNow('2026-09-26 09:00:00'); // 25 jours : revue qui approche
        $keys = $this->keys(app(ReminderResolver::class)->for($user));
        $this->assertContains('review_soon', $keys);
        $this->assertNotContains('review', $keys);
    }

    public function test_un_groupe_silencieux_est_rappele_apres_21_jours(): void
    {
        CarbonImmutable::setTestNow('2026-09-01 09:00:00');
        $user = $this->participant();
        $this->diagnosed($user);
        $this->anchored($user);
        $group = WorkshopGroup::create(['name' => 'G', 'code' => 'ABCDEF', 'created_by' => $user->id]);
        $group->members()->attach($user->id, ['joined_at' => now()]);

        $this->assertNotContains('group_silent', $this->keys(app(ReminderResolver::class)->for($user)));

        CarbonImmutable::setTestNow('2026-09-23 09:00:00');
        $this->assertContains('group_silent', $this->keys(app(ReminderResolver::class)->for($user)));
    }

    public function test_la_commande_hebdomadaire_envoie_un_email_aux_participants_concernes(): void
    {
        Mail::fake();
        $withReminder = $this->participant();
        $this->diagnosed($withReminder);
        $this->anchored($withReminder);

        $this->artisan('workshop:weekly-reminders')->assertSuccessful();

        Mail::assertSent(WorkshopWeeklyReminder::class, fn ($mail) => $mail->hasTo($withReminder->email) && count($mail->reminders) >= 1);
        Mail::assertSent(WorkshopWeeklyReminder::class, 1);
    }
}
