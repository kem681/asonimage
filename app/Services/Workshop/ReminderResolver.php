<?php

namespace App\Services\Workshop;

use App\Models\User;
use Carbon\CarbonImmutable;

/**
 * Calcule, a la volee, les rappels dus pour un participant. Aucun rappel
 * n'est stocke : tout se deduit de l'etat courant (diagnostic, ancrage,
 * pointages, frottements, revues, groupes).
 *
 * Chaque rappel : ['key' => ..., 'title' => ..., 'body' => ..., 'route' => nom de route, 'params' => [...]]
 */
class ReminderResolver
{
    /** @return list<array<string, mixed>> */
    public function for(User $user, ?CarbonImmutable $now = null): array
    {
        $now ??= WorkshopClock::now();
        $today = $now->startOfDay();
        $reminders = [];

        $diagnostic = $user->latestDiagnostic();

        if (! $diagnostic) {
            $reminders[] = $this->reminder('diagnostic', 'Commence par le diagnostic', "Trois à quatre minutes pour savoir où ça tire le plus aujourd'hui.", 'workshop.diagnostic');

            return $reminders;
        }

        if (! $diagnostic->hasAxis()) {
            $reminders[] = $this->reminder('choose_axis', 'Choisis ton axe phare', 'Deux axes sont à égalité : à toi de dire par lequel commencer.', 'workshop.diagnostic.result');

            return $reminders;
        }

        $anchor = $user->activeAnchor();

        if (! $anchor) {
            $reminders[] = $this->reminder('anchor', 'Pose ton geste', 'Un seul geste, minuscule, tenable même un mauvais jour.', 'workshop.anchor.create');
        } else {
            if (! $anchor->checkinFor($today)) {
                $reminders[] = $this->reminder('checkin', 'Le geste du jour', 'Tenu ou pas tenu ? Une seule réponse, sans commentaire.', 'workshop.index');
            }

            if (! $anchor->frictionForWeek(WorkshopClock::weekStart($today))) {
                $reminders[] = $this->reminder('friction', 'Le frottement de la semaine', "Où la résistance s'est-elle manifestée ? Et à qui tu l'as dit.", 'workshop.friction.create');
            }

            if ($anchor->isReviewDue($today)) {
                $reminders[] = $this->reminder('review', 'La revue est ouverte', 'Quatre semaines ont passé. Regarde en arrière et nomme ce qui a tenu.', 'workshop.review.create');
            } elseif ($anchor->isReviewApproaching($today)) {
                $days = $anchor->daysUntilReview($today);
                $reminders[] = $this->reminder('review_soon', 'Revue dans '.$days.' jour'.($days > 1 ? 's' : ''), 'Prévois un moment avec ton binôme pour la faire ensemble.', 'workshop.path');
            }
        }

        foreach ($user->workshopGroups as $group) {
            if ($group->isSilent($now)) {
                $days = $group->daysSinceLastContact($now);
                $reminders[] = $this->reminder(
                    'group_silent',
                    'Groupe « '.$group->name.' » silencieux',
                    "Vous n'avez pas déclaré d'échange depuis {$days} jours. Un message, un appel, un café.",
                    'workshop.group.show',
                    ['group' => $group->id],
                );
            }
        }

        return $reminders;
    }

    /** @return array<string, mixed> */
    private function reminder(string $key, string $title, string $body, string $route, array $params = []): array
    {
        return compact('key', 'title', 'body', 'route', 'params');
    }
}
