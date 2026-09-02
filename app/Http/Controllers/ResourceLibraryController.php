<?php

namespace App\Http\Controllers;

use App\Models\Edition;
use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class ResourceLibraryController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $editions = Edition::withCount('resources')
            ->when(! $user->is_admin, function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->where('is_common', true)->orWhere('id', $user->edition_id);
                });
            })
            ->orderByDesc('is_common')
            ->orderByDesc('year')
            ->get();

        return view('membres.editions', ['editions' => $editions]);
    }

    public function showEdition(Request $request, Edition $edition): View
    {
        abort_unless($request->user()->canAccessEdition($edition), 403);

        $counts = $edition->resources()
            ->selectRaw('day, count(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        return view('membres.jours', [
            'edition' => $edition,
            'counts' => $counts,
        ]);
    }

    public function showDay(Request $request, Edition $edition, int $day): View
    {
        abort_unless($request->user()->canAccessEdition($edition), 403);
        abort_unless($day >= 1 && $day <= 5, 404);

        $resources = $edition->resources()->where('day', $day)->orderBy('title')->get();

        return view('membres.ressources', [
            'edition' => $edition,
            'day' => $day,
            'resources' => $resources,
        ]);
    }

    public function show(Request $request, Resource $resource): View
    {
        abort_unless($request->user()->canAccessEdition($resource->edition), 403);

        return view('membres.ressource', ['resource' => $resource]);
    }

    public function stream(Request $request, Resource $resource): BinaryFileResponse
    {
        abort_unless($request->user()->canAccessEdition($resource->edition), 403);
        abort_unless(Storage::disk('public')->exists($resource->file_path), 404);

        // Servi en "inline" (jamais "attachment") : le fichier s'ouvre dans le
        // navigateur pour consultation, il ne se telecharge pas directement.
        // BinaryFileResponse (plutot qu'un StreamedResponse) gere nativement
        // les requetes "Range", necessaires pour le seek audio et un
        // chargement progressif cote pdf.js sur les gros fichiers.
        $response = new BinaryFileResponse(Storage::disk('public')->path($resource->file_path));
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_INLINE, $resource->original_filename);

        return $response;
    }
}
