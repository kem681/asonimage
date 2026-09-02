<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Models\WorkshopCode;
use App\Models\WorkshopParticipant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Saisie du code d'atelier par un utilisateur deja connecte (par exemple un
 * membre du seminaire qui vient de suivre un atelier 3x30).
 */
class JoinController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if ($request->user()->canAccessWorkshop()) {
            return redirect()->route('workshop.index');
        }

        return view('workshop.join');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'workshop_code' => ['required', 'string', 'max:32'],
        ]);

        $code = WorkshopCode::findActive($data['workshop_code']);

        if (! $code) {
            return back()->withInput()->withErrors([
                'workshop_code' => "Ce code d'atelier n'est pas reconnu (ou n'est plus actif).",
            ]);
        }

        $user = $request->user();

        WorkshopParticipant::firstOrCreate(
            ['user_id' => $user->id],
            ['workshop_code_id' => $code->id, 'email' => $user->email, 'joined_at' => now()],
        );

        return redirect()->route('workshop.index')->with('status', 'Bienvenue dans 3x30.');
    }
}
