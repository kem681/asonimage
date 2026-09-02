<?php

namespace App\Services\Workshop;

use Carbon\CarbonImmutable;

/**
 * Horloge du module 3x30 : toutes les dates "du jour" sont calculees en heure
 * suisse, quel que soit le fuseau du serveur. Carbon::setTestNow() reste
 * respecte, ce qui permet de piloter le temps dans les tests.
 */
class WorkshopClock
{
    const TIMEZONE = 'Europe/Zurich';

    public static function now(): CarbonImmutable
    {
        return CarbonImmutable::now(self::TIMEZONE);
    }

    public static function today(): CarbonImmutable
    {
        return self::now()->startOfDay();
    }

    public static function yesterday(): CarbonImmutable
    {
        return self::today()->subDay();
    }

    /** Lundi de la semaine en cours (semaine ISO). */
    public static function weekStart(?CarbonImmutable $day = null): CarbonImmutable
    {
        return ($day ?? self::today())->startOfWeek(CarbonImmutable::MONDAY)->startOfDay();
    }
}
