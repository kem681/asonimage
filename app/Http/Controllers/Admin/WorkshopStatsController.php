<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Anchor;
use App\Models\Diagnostic;
use App\Models\Review;
use App\Models\WorkshopGroup;
use App\Models\WorkshopParticipant;
use App\Services\Workshop\WorkshopContent;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Chiffres agreges et anonymes, pour ajuster l'atelier. Aucune donnee
 * individuelle, aucun contenu ecrit n'est lu ici.
 */
class WorkshopStatsController extends Controller
{
    public function index(WorkshopContent $content): View
    {
        $axisCounts = Diagnostic::query()
            ->whereNotNull('axis')
            ->whereIn('id', function ($query) {
                // Le dernier diagnostic de chaque participant seulement.
                $query->select(DB::raw('max(id)'))->from('diagnostics')->groupBy('user_id');
            })
            ->select('axis', DB::raw('count(*) as total'))
            ->groupBy('axis')
            ->pluck('total', 'axis');

        return view('admin.workshop.stats', [
            'content' => $content,
            'participants' => WorkshopParticipant::count(),
            'diagnostics' => Diagnostic::count(),
            'usersWithDiagnostic' => Diagnostic::distinct('user_id')->count('user_id'),
            'axisCounts' => $axisCounts,
            'activeAnchors' => Anchor::where('is_active', true)->count(),
            'reviews' => Review::count(),
            'groups' => WorkshopGroup::count(),
            'groupMembers' => DB::table('workshop_group_members')->count(),
        ]);
    }
}
