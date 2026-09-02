<?php

namespace App\Models;

use App\Services\Workshop\WorkshopClock;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[Fillable(['name', 'code', 'created_by', 'last_contact_at', 'next_meeting_at'])]
class WorkshopGroup extends Model
{
    const MAX_MEMBERS = 12;

    /** Nombre de jours sans echange declare a partir duquel le groupe est "silencieux". */
    const SILENT_AFTER_DAYS = 21;

    /** Alphabet sans caracteres ambigus (pas de 0/O, 1/I/L). */
    const CODE_ALPHABET = 'ABCDEFGHJKMNPQRSTUVWXYZ23456789';

    const CODE_LENGTH = 6;

    protected function casts(): array
    {
        return [
            'last_contact_at' => 'datetime',
            'next_meeting_at' => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'workshop_group_members')
            ->withPivot('joined_at')
            ->withTimestamps()
            ->orderBy('name');
    }

    public function isFull(): bool
    {
        return $this->members()->count() >= self::MAX_MEMBERS;
    }

    public function hasMember(User $user): bool
    {
        return $this->members()->whereKey($user->id)->exists();
    }

    /** Date de reference du dernier echange : le dernier contact declare, sinon la creation. */
    public function lastActivityAt(): CarbonImmutable
    {
        $reference = $this->last_contact_at ?? $this->created_at ?? WorkshopClock::now();

        return CarbonImmutable::instance($reference);
    }

    public function isSilent(?CarbonImmutable $now = null): bool
    {
        $now ??= WorkshopClock::now();

        return $this->lastActivityAt()->addDays(self::SILENT_AFTER_DAYS)->lessThanOrEqualTo($now);
    }

    public function daysSinceLastContact(?CarbonImmutable $now = null): int
    {
        $now ??= WorkshopClock::now();

        return (int) $this->lastActivityAt()->diffInDays($now);
    }

    public static function generateCode(): string
    {
        do {
            $code = '';
            for ($i = 0; $i < self::CODE_LENGTH; $i++) {
                $code .= self::CODE_ALPHABET[random_int(0, strlen(self::CODE_ALPHABET) - 1)];
            }
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public static function normalizeCode(string $code): string
    {
        return strtoupper(trim(str_replace([' ', '-'], '', $code)));
    }
}
