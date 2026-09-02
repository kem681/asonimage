<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Models\Anchor;
use App\Models\AnchorCheckin;
use App\Services\Workshop\WorkshopClock;
use App\Services\Workshop\WorkshopContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AnchorController extends Controller
{
    public function create(Request $request, WorkshopContent $content): View|RedirectResponse
    {
        $user = $request->user();
        $diagnostic = $user->latestDiagnostic();

        if (! $diagnostic || ! $diagnostic->hasAxis()) {
            return redirect()->route('workshop.diagnostic')->with('status', "Commence par le diagnostic : c'est lui qui désigne l'axe par lequel entrer.");
        }

        $requested = (string) $request->query('axe', '');
        $axis = $content->hasAxis($requested) ? $requested : $diagnostic->axis;

        return view('workshop.anchor.create', [
            'content' => $content,
            'axis' => $axis,
            'diagnostic' => $diagnostic,
            'current' => $user->activeAnchor(),
        ]);
    }

    public function store(Request $request, WorkshopContent $content): RedirectResponse
    {
        $data = $request->validate([
            'axis' => ['required', Rule::in($content->axisKeys())],
            'manquement' => ['required', 'string', 'max:500'],
            'gesture' => ['required', 'string', 'max:500'],
            'confidant' => ['required', 'string', 'max:120'],
        ], [
            'manquement.required' => 'Nomme ton manquement en une phrase.',
            'gesture.required' => 'Écris le geste, un seul.',
            'confidant.required' => 'Écris le prénom de la personne à qui tu le dis.',
        ]);

        $user = $request->user();

        $user->activeAnchor()?->close();

        Anchor::create([
            'user_id' => $user->id,
            'axis' => $data['axis'],
            'manquement' => $data['manquement'],
            'gesture' => $data['gesture'],
            'confidant' => $data['confidant'],
            'started_on' => WorkshopClock::today()->toDateString(),
            'is_active' => true,
        ]);

        return redirect()->route('workshop.index')->with('status', 'Ton geste est posé. Un jour à la fois.');
    }

    /** Pointage "tenu / pas tenu", pour aujourd'hui ou hier seulement. */
    public function checkin(Request $request): RedirectResponse
    {
        $anchor = $request->user()->activeAnchor();

        abort_unless($anchor, 404);

        $today = WorkshopClock::today();
        $yesterday = WorkshopClock::yesterday();

        $data = $request->validate([
            'day' => ['required', 'date', Rule::in([$today->toDateString(), $yesterday->toDateString()])],
            'held' => ['required', 'boolean'],
        ], [
            'day.in' => "Le pointage ne se fait que pour aujourd'hui ou hier.",
        ]);

        // Recherche par date (et non par egalite de chaine) : selon la base, la
        // colonne date peut etre stockee avec ou sans heure.
        $checkin = AnchorCheckin::where('anchor_id', $anchor->id)->whereDate('day', $data['day'])->first()
            ?? new AnchorCheckin(['anchor_id' => $anchor->id, 'day' => $data['day']]);

        $checkin->held = (bool) $data['held'];
        $checkin->save();

        return redirect()->route('workshop.index');
    }
}
