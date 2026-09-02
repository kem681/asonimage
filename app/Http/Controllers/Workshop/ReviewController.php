<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Services\Workshop\WorkshopClock;
use App\Services\Workshop\WorkshopContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function create(Request $request, WorkshopContent $content): View|RedirectResponse
    {
        $anchor = $request->user()->activeAnchor();

        if (! $anchor) {
            return redirect()->route('workshop.index');
        }

        if (! $anchor->isReviewDue()) {
            $days = $anchor->daysUntilReview();

            return redirect()->route('workshop.path')->with('status', "La revue s'ouvre dans {$days} jour".($days > 1 ? 's' : '').". D'ici là, un jour à la fois.");
        }

        $cycleStart = $anchor->cycleStartedOn();

        return view('workshop.review.create', [
            'content' => $content,
            'anchor' => $anchor,
            'cycleStart' => $cycleStart,
            'days' => $anchor->cycleDays(),
            'frictions' => $anchor->frictions()->whereDate('week_start', '>=', $cycleStart->subDays(6)->toDateString())->orderBy('week_start')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $anchor = $request->user()->activeAnchor();

        abort_unless($anchor, 404);

        if (! $anchor->isReviewDue()) {
            return redirect()->route('workshop.path')->withErrors(['review' => "La revue n'est pas encore ouverte."]);
        }

        $data = $request->validate([
            'held' => ['required', Rule::in(array_keys(Review::HELD_LABELS))],
            'changed' => ['required', 'string', 'max:1000'],
            'next_friction' => ['nullable', 'string', 'max:1000'],
            'decision' => ['required', Rule::in(Review::DECISIONS)],
        ], [
            'changed.required' => 'Une phrase suffit : ce que ça a changé, ou pas.',
        ]);

        Review::create([
            'anchor_id' => $anchor->id,
            'held' => $data['held'],
            'changed' => $data['changed'],
            'next_friction' => $data['next_friction'] ?? null,
            'decision' => $data['decision'],
            'reviewed_on' => WorkshopClock::today()->toDateString(),
        ]);

        return match ($data['decision']) {
            Review::DECISION_NEW_GESTURE => tap(redirect()->route('workshop.anchor.create', ['axe' => $anchor->axis]), fn () => $anchor->close())
                ->with('status', 'Revue gardée en mémorial. Pose ton nouveau geste.'),
            Review::DECISION_DIAGNOSTIC => tap(redirect()->route('workshop.diagnostic'), fn () => $anchor->close())
                ->with('status', 'Revue gardée en mémorial. Refais le diagnostic pour voir où ça tire maintenant.'),
            default => redirect()->route('workshop.memorial')
                ->with('status', 'Revue gardée en mémorial. Le geste continue, un jour à la fois.'),
        };
    }

    /** Le memorial : toutes les revues, dans l'ordre, relisibles. */
    public function index(Request $request, WorkshopContent $content): View
    {
        $user = $request->user();

        $reviews = Review::query()
            ->whereIn('anchor_id', $user->anchors()->select('id'))
            ->with('anchor')
            ->orderBy('reviewed_on')
            ->orderBy('id')
            ->get();

        return view('workshop.memorial', [
            'content' => $content,
            'reviews' => $reviews,
        ]);
    }
}
