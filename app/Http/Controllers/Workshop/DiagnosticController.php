<?php

namespace App\Http\Controllers\Workshop;

use App\Http\Controllers\Controller;
use App\Models\Diagnostic;
use App\Services\Workshop\DiagnosticScorer;
use App\Services\Workshop\WorkshopContent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use InvalidArgumentException;

class DiagnosticController extends Controller
{
    public function show(WorkshopContent $content): View
    {
        return view('workshop.diagnostic.form', ['content' => $content]);
    }

    public function store(Request $request, WorkshopContent $content, DiagnosticScorer $scorer): RedirectResponse
    {
        $count = $content->statementCount();

        $data = $request->validate([
            'answers' => ['required', 'array', 'size:'.$count],
            'answers.*' => ['required', 'integer', 'between:'.$content->scaleMin().','.$content->scaleMax()],
        ], [
            'answers.size' => 'Réponds à toutes les affirmations avant de valider.',
            'answers.*.required' => 'Réponds à toutes les affirmations avant de valider.',
        ]);

        try {
            $result = $scorer->score($data['answers']);
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['answers' => 'Réponds à toutes les affirmations avant de valider.']);
        }

        Diagnostic::create([
            'user_id' => $request->user()->id,
            'answers' => array_map('intval', $data['answers']),
            'score_filiation' => $result['scores']['filiation'],
            'score_desert' => $result['scores']['desert'],
            'score_appel' => $result['scores']['appel'],
            'axis' => count($result['leading']) === 1 ? $result['leading'][0] : null,
            'completed_at' => now(),
        ]);

        return redirect()->route('workshop.diagnostic.result');
    }

    public function result(Request $request, WorkshopContent $content): View|RedirectResponse
    {
        $diagnostic = $request->user()->latestDiagnostic();

        if (! $diagnostic) {
            return redirect()->route('workshop.diagnostic');
        }

        return view('workshop.diagnostic.result', [
            'content' => $content,
            'diagnostic' => $diagnostic,
            'leading' => $diagnostic->leadingAxes(),
            'anchor' => $request->user()->activeAnchor(),
        ]);
    }

    /** En cas d'egalite entre axes, l'utilisateur choisit son axe phare. */
    public function chooseAxis(Request $request): RedirectResponse
    {
        $diagnostic = $request->user()->latestDiagnostic();

        abort_unless($diagnostic, 404);

        $data = $request->validate([
            'axis' => ['required', Rule::in($diagnostic->leadingAxes())],
        ]);

        $diagnostic->update(['axis' => $data['axis']]);

        return redirect()->route('workshop.diagnostic.result');
    }
}
