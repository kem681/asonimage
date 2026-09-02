<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Models\WorkshopGroup;
use App\Services\Workshop\WorkshopClock;
use App\Services\Workshop\WorkshopContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GroupController extends Controller
{
    public function index(Request $request, WorkshopContent $content): View
    {
        return view('workshop.group.index', [
            'content' => $content,
            'groups' => $request->user()->workshopGroups()->withCount('members')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
        ], [
            'name.required' => 'Donne un nom à ton groupe, même simple.',
        ]);

        $user = $request->user();

        $group = WorkshopGroup::create([
            'name' => $data['name'],
            'code' => WorkshopGroup::generateCode(),
            'created_by' => $user->id,
        ]);

        $group->members()->attach($user->id, ['joined_at' => now()]);

        return redirect()->route('workshop.group.show', $group)->with('status', 'Groupe créé. Donne le code à tes compagnons.');
    }

    public function join(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'code' => ['required', 'string', 'max:12'],
        ]);

        $group = WorkshopGroup::where('code', WorkshopGroup::normalizeCode($data['code']))->first();

        if (! $group) {
            return back()->withInput()->withErrors(['code' => 'Aucun groupe avec ce code. Vérifie-le avec celui qui te l\'a donné.']);
        }

        $user = $request->user();

        if ($group->hasMember($user)) {
            return redirect()->route('workshop.group.show', $group);
        }

        if ($group->isFull()) {
            return back()->withInput()->withErrors(['code' => 'Ce groupe est complet ('.WorkshopGroup::MAX_MEMBERS.' membres).']);
        }

        $group->members()->attach($user->id, ['joined_at' => now()]);

        return redirect()->route('workshop.group.show', $group)->with('status', 'Tu as rejoint « '.$group->name.' ».');
    }

    public function show(Request $request, WorkshopContent $content, WorkshopGroup $group): View
    {
        $this->ensureMember($request, $group);

        return view('workshop.group.show', [
            'content' => $content,
            'group' => $group,
            'members' => $group->members()->get(),
            'now' => WorkshopClock::now(),
        ]);
    }

    /** « On s'est parlé » : n'importe quel membre peut le declarer. */
    public function contact(Request $request, WorkshopGroup $group): RedirectResponse
    {
        $this->ensureMember($request, $group);

        $group->update(['last_contact_at' => now()]);

        return back()->with('status', 'Noté. Le lien tient.');
    }

    public function meeting(Request $request, WorkshopGroup $group): RedirectResponse
    {
        $this->ensureMember($request, $group);

        $data = $request->validate([
            'next_meeting_at' => ['nullable', 'date'],
        ]);

        $group->update([
            'next_meeting_at' => ! empty($data['next_meeting_at'])
                ? WorkshopClock::now()->parse($data['next_meeting_at'], WorkshopClock::TIMEZONE)->utc()
                : null,
        ]);

        return back()->with('status', ! empty($data['next_meeting_at']) ? 'Rendez-vous fixé.' : 'Rendez-vous effacé.');
    }

    public function leave(Request $request, WorkshopGroup $group): RedirectResponse
    {
        $this->ensureMember($request, $group);

        $group->members()->detach($request->user()->id);

        if ($group->members()->count() === 0) {
            $group->delete();
        }

        return redirect()->route('workshop.group.index')->with('status', 'Tu as quitté le groupe.');
    }

    private function ensureMember(Request $request, WorkshopGroup $group): void
    {
        abort_unless($group->hasMember($request->user()), 404);
    }
}
