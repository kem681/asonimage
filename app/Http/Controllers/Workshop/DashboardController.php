<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Services\Workshop\ReminderResolver;
use App\Services\Workshop\WorkshopClock;
use App\Services\Workshop\WorkshopContent;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(Request $request, ReminderResolver $reminders, WorkshopContent $content): View
    {
        $user = $request->user();
        $diagnostic = $user->latestDiagnostic();
        $anchor = $user->activeAnchor();

        $today = WorkshopClock::today();
        $yesterday = WorkshopClock::yesterday();

        return view('workshop.index', [
            'content' => $content,
            'diagnostic' => $diagnostic,
            'anchor' => $anchor,
            'today' => $today,
            'yesterday' => $yesterday,
            'todayCheckin' => $anchor?->checkinFor($today),
            'yesterdayCheckin' => $anchor?->checkinFor($yesterday),
            'weekFriction' => $anchor?->frictionForWeek(WorkshopClock::weekStart($today)),
            'reminders' => $reminders->for($user),
            'groups' => $user->workshopGroups,
        ]);
    }
}
