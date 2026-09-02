<?php

namespace App\Models;

use App\Services\Workshop\WorkshopClock;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['user_id', 'axis', 'manquement', 'gesture', 'confidant', 'started_on', 'ended_on', 'is_active'])]
class Anchor extends Model
{
    /** Nombre de jours entre le debut d'un cycle et l'ouverture de la revue. */
    const REVIEW_AFTER_DAYS = 28;

    /** Nombre de jours avant la revue a partir duquel on la rappelle. */
    const REVIEW_REMIND_FROM_DAYS = 25;

    protected function casts(): array
    {
        return [
            'started_on' => 'immutable_date',
            'ended_on' => 'immutable_date',
            'is_active' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(AnchorCheckin::class);
    }

    public function frictions(): HasMany
    {
        return $this->hasMany(Friction::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function checkinFor(CarbonImmutable $day): ?AnchorCheckin
    {
        return $this->checkins()->whereDate('day', $day->toDateString())->first();
    }

    public function frictionForWeek(CarbonImmutable $weekStart): ?Friction
    {
        return $this->frictions()->whereDate('week_start', $weekStart->toDateString())->first();
    }

    /**
     * Debut du cycle en cours : la derniere revue si elle existe, sinon le debut
     * de l'ancrage. Toujours exprime en heure suisse, comme WorkshopClock, pour
     * que les comparaisons de jours ne dependent pas du fuseau du serveur.
     */
    public function cycleStartedOn(): CarbonImmutable
    {
        $lastReview = $this->reviews()->orderByDesc('reviewed_on')->orderByDesc('id')->first();

        return self::asLocalDate($lastReview ? $lastReview->reviewed_on : $this->started_on);
    }

    /** Convertit une date (Carbon ou chaine Y-m-d) en minuit, heure suisse. */
    public static function asLocalDate(CarbonImmutable|string $date): CarbonImmutable
    {
        $string = $date instanceof CarbonImmutable ? $date->toDateString() : substr((string) $date, 0, 10);

        return CarbonImmutable::parse($string, WorkshopClock::TIMEZONE)->startOfDay();
    }

    public function reviewDueOn(): CarbonImmutable
    {
        return $this->cycleStartedOn()->addDays(self::REVIEW_AFTER_DAYS);
    }

    public function isReviewDue(?CarbonImmutable $today = null): bool
    {
        $today ??= WorkshopClock::today();

        return $today->greaterThanOrEqualTo($this->reviewDueOn());
    }

    public function isReviewApproaching(?CarbonImmutable $today = null): bool
    {
        $today ??= WorkshopClock::today();
        $remindFrom = $this->cycleStartedOn()->addDays(self::REVIEW_REMIND_FROM_DAYS);

        return $today->greaterThanOrEqualTo($remindFrom) && ! $this->isReviewDue($today);
    }

    public function daysUntilReview(?CarbonImmutable $today = null): int
    {
        $today ??= WorkshopClock::today();

        return max(0, (int) $today->diffInDays($this->reviewDueOn(), false));
    }

    /**
     * Les jours du cycle en cours, du debut a aujourd'hui, avec leur pointage
     * eventuel. Aucun compte n'est calcule : c'est une lecture jour par jour.
     *
     * @return list<array{date: CarbonImmutable, checkin: ?AnchorCheckin}>
     */
    public function cycleDays(?CarbonImmutable $today = null): array
    {
        $today ??= WorkshopClock::today();
        $cycleStart = $this->cycleStartedOn();

        $checkins = $this->checkins()
            ->whereDate('day', '>=', $cycleStart->toDateString())
            ->get()
            ->keyBy(fn (AnchorCheckin $checkin) => $checkin->day->toDateString());

        $days = [];
        for ($day = $cycleStart; $day->lessThanOrEqualTo($today); $day = $day->addDay()) {
            $days[] = ['date' => $day, 'checkin' => $checkins->get($day->toDateString())];
        }

        return $days;
    }

    public function close(?CarbonImmutable $on = null): void
    {
        $this->forceFill([
            'is_active' => false,
            'ended_on' => ($on ?? WorkshopClock::today())->toDateString(),
        ])->save();
    }
}
