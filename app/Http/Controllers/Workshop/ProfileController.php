<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Models\WorkshopGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = $request->user();

        return view('workshop.profile', [
            'user' => $user,
            'participant' => $user->workshopParticipant,
            'anchorsCount' => $user->anchors()->count(),
            'groupsCount' => $user->workshopGroups()->count(),
        ]);
    }

    /**
     * Suppression totale : le compte et tout ce qu'il a ecrit (diagnostics,
     * ancrages, pointages, frottements, revues, appartenance aux groupes).
     * Rien n'est conserve.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'confirmation' => ['required', 'in:SUPPRIMER'],
        ], [
            'confirmation.in' => 'Écris SUPPRIMER en majuscules pour confirmer.',
        ]);

        $user = $request->user();
        $groupIds = $user->workshopGroups()->pluck('workshop_groups.id');

        Auth::logout();

        $user->delete();

        // Les groupes que l'utilisateur laisse vides disparaissent avec lui.
        WorkshopGroup::whereIn('id', $groupIds)->doesntHave('members')->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('landing')->with('status', 'Ton compte et toutes tes données ont été supprimés.');
    }
}
