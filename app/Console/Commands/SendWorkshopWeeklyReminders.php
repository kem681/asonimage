<?php

namespace App\Console\Commands;

use App\Mail\WorkshopWeeklyReminder;
use App\Models\WorkshopParticipant;
use App\Services\Workshop\ReminderResolver;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWorkshopWeeklyReminders extends Command
{
    protected $signature = 'workshop:weekly-reminders';

    protected $description = "Envoie l'email hebdomadaire 3x30 aux participants qui ont un rappel en attente";

    public function handle(ReminderResolver $resolver): int
    {
        $sent = 0;

        WorkshopParticipant::with('user')->chunk(100, function ($participants) use ($resolver, &$sent) {
            foreach ($participants as $participant) {
                $user = $participant->user;

                if (! $user) {
                    continue;
                }

                $reminders = $resolver->for($user);

                if (empty($reminders)) {
                    continue;
                }

                Mail::to($user->email, $user->name)->send(new WorkshopWeeklyReminder($user, $reminders));
                $sent++;
            }
        });

        $this->info("Rappels envoyés : {$sent}");

        return self::SUCCESS;
    }
}
