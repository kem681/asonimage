<?php

namespace Tests\Concerns;

use App\Models\Anchor;
use App\Models\Diagnostic;
use App\Models\User;
use App\Models\WorkshopCode;
use App\Models\WorkshopParticipant;
use App\Services\Workshop\WorkshopClock;

trait CreatesWorkshopUsers
{
    protected function workshopCode(string $code = 'TEST2026', bool $active = true): WorkshopCode
    {
        return WorkshopCode::create([
            'code' => $code,
            'label' => 'Atelier de test',
            'is_active' => $active,
        ]);
    }

    protected function participant(array $attributes = []): User
    {
        $user = User::factory()->create($attributes);

        WorkshopParticipant::create([
            'user_id' => $user->id,
            'workshop_code_id' => $this->workshopCode('P'.strtoupper(substr(md5((string) $user->id), 0, 6)))->id,
            'email' => $user->email,
            'joined_at' => now(),
        ]);

        return $user;
    }

    protected function diagnosed(User $user, string $axis = 'desert'): Diagnostic
    {
        return Diagnostic::create([
            'user_id' => $user->id,
            'answers' => array_fill(0, 24, 2),
            'score_filiation' => 30,
            'score_desert' => 70,
            'score_appel' => 50,
            'axis' => $axis,
            'completed_at' => now(),
        ]);
    }

    protected function anchored(User $user, array $attributes = []): Anchor
    {
        return Anchor::create(array_merge([
            'user_id' => $user->id,
            'axis' => 'desert',
            'manquement' => 'Je fuis le silence.',
            'gesture' => 'Cinq minutes sans écran.',
            'confidant' => 'Marc',
            'started_on' => WorkshopClock::today()->toDateString(),
            'is_active' => true,
        ], $attributes));
    }
}
