<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Models\Friction;
use App\Services\Workshop\WorkshopClock;
use App\Services\Workshop\WorkshopContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrictionController extends Controller
{
    public function create(Request $request, WorkshopContent $content): View|RedirectResponse
    {
        $anchor = $request->user()->activeAnchor();

        if (! $anchor) {
            return redirect()->route('workshop.index')->with('status', "Pose d'abord ton geste : le frottement se nomme par rapport à lui.");
        }

        $weekStart = WorkshopClock::weekStart();

        return view('workshop.friction.create', [
            'content' => $content,
            'anchor' => $anchor,
            'weekStart' => $weekStart,
            'friction' => $anchor->frictionForWeek($weekStart),
            'previous' => $anchor->frictions()->where('week_start', '<', $weekStart->toDateString())->orderByDesc('week_start')->limit(4)->get(),
        ]);
    }

    /** Une entree par semaine et par ancrage : on cree ou on met a jour celle de la semaine. */
    public function store(Request $request): RedirectResponse
    {
        $anchor = $request->user()->activeAnchor();

        abort_unless($anchor, 404);

        $data = $request->validate([
            'body' => ['required', 'string', 'max:1000'],
            'told_to' => ['nullable', 'string', 'max:120'],
            'told' => ['nullable', 'boolean'],
        ], [
            'body.required' => "Écris où la résistance s'est manifestée, même en une ligne.",
        ]);

        $told = (bool) ($data['told'] ?? false) && ! empty($data['told_to']);

        $weekStart = WorkshopClock::weekStart();

        $friction = $anchor->frictionForWeek($weekStart)
            ?? new Friction(['anchor_id' => $anchor->id, 'week_start' => $weekStart->toDateString()]);

        $friction->fill([
            'body' => $data['body'],
            'told_to' => $data['told_to'] ?? null,
            'told_on' => $told ? WorkshopClock::today()->toDateString() : null,
        ])->save();

        return redirect()->route('workshop.index')->with('status', $told
            ? 'Frottement nommé, et dit. Bien.'
            : "Frottement nommé. Il attend d'être dit à quelqu'un.");
    }

    /** Marquer un frottement comme dit a quelqu'un. */
    public function told(Request $request, Friction $friction): RedirectResponse
    {
        abort_unless($friction->anchor->user_id === $request->user()->id, 404);

        $data = $request->validate([
            'told_to' => ['required', 'string', 'max:120'],
        ], [
            'told_to.required' => 'Écris le prénom de la personne à qui tu l\'as dit.',
        ]);

        $friction->update([
            'told_to' => $data['told_to'],
            'told_on' => WorkshopClock::today()->toDateString(),
        ]);

        return back()->with('status', 'Dit. Ça change tout.');
    }
}
