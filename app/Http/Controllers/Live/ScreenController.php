<?php

namespace App\Http\Controllers\Live;

use App\Http\Controllers\Controller;
use App\Models\LiveQuestion;
use App\Models\LiveSession;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * Page ecran : asonimage.ch/live/ecran, a projeter. Lecture seule.
 * Montre le code pour rejoindre, puis la question en cours et ses resultats quand l'animateur les affiche.
 */
class ScreenController extends Controller
{
    public function show(): View
    {
        return view('live.screen', ['session' => LiveSession::active()]);
    }

    public function state(): JsonResponse
    {
        $session = LiveSession::active();
        if (! $session) {
            return response()->json(['session' => null, 'question' => null]);
        }

        $question = $session->current_question_id ? LiveQuestion::find($session->current_question_id) : null;

        return response()->json([
            'session' => [
                'code' => $session->code,
                'title' => $session->title,
                'join_url' => $session->joinUrl(),
                'short_url' => $session->shortJoinUrl(),
            ],
            'question' => $question?->toPublicArray(),
            'count' => $question ? $question->answers()->count() : 0,
            'results' => $question && $question->show_results ? $question->results() : null,
        ]);
    }
}
