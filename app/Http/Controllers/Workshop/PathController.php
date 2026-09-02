<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Services\Workshop\WorkshopClock;
use App\Services\Workshop\WorkshopContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Le chemin : l'ancrage en cours, ses pointages, ses frottements, sa prochaine revue. */
class PathController extends Controller
{
    public function index(Request $request, WorkshopContent $content): View|RedirectResponse
    {
        $anchor = $request->user()->activeAnchor();

        if (! $anchor) {
            return redirect()->route('workshop.index');
        }

        $today = WorkshopClock::today();

        return view('workshop.path', [
            'content' => $content,
            'anchor' => $anchor,
            'cycleStart' => $anchor->cycleStartedOn(),
            'days' => $anchor->cycleDays($today),
            'frictions' => $anchor->frictions()->orderByDesc('week_start')->get(),
            'reviews' => $anchor->reviews()->orderByDesc('reviewed_on')->get(),
            'reviewDueOn' => $anchor->reviewDueOn(),
            'reviewDue' => $anchor->isReviewDue($today),
            'daysUntilReview' => $anchor->daysUntilReview($today),
        ]);
    }
}
