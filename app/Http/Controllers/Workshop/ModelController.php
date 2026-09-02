<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Services\Workshop\WorkshopContent;
use Illuminate\Http\Request;
use Illuminate\View\View;

/** Lecture libre du modele : les trois axes, consultables quel que soit l'axe phare. */
class ModelController extends Controller
{
    public function index(Request $request, WorkshopContent $content): View
    {
        return view('workshop.model.index', [
            'content' => $content,
            'diagnostic' => $request->user()->latestDiagnostic(),
        ]);
    }

    public function show(Request $request, WorkshopContent $content, string $axis): View
    {
        abort_unless($content->hasAxis($axis), 404);

        return view('workshop.model.axis', [
            'content' => $content,
            'key' => $axis,
            'axis' => $content->axis($axis),
            'diagnostic' => $request->user()->latestDiagnostic(),
        ]);
    }
}
