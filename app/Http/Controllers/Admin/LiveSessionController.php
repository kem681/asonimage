<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveQuestion;
use App\Models\LiveSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Pilotage des sondages en direct : asonimage.ch/sondage (admin connecte).
 * Pense pour etre utilise depuis un telephone pendant l'atelier.
 */
class LiveSessionController extends Controller
{
    public function index(): View
    {
        return view('live.admin.index', [
            'sessions' => LiveSession::withCount('questions')->latest()->get(),
            'templates' => LiveSession::templates(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'template' => ['nullable', 'string', 'regex:/^[a-z0-9-]+$/'],
            'title' => ['nullable', 'string', 'max:160'],
        ]);

        if (! empty($data['template'])) {
            $session = LiveSession::createFromTemplate($data['template']);
        } else {
            $session = LiveSession::create([
                'code' => LiveSession::generateCode(),
                'title' => $data['title'] ?: 'Sondage sans titre',
            ]);
        }

        return redirect()->route('admin.live.show', $session)->with('status', "Sondage créé, code {$session->code}.");
    }

    public function show(LiveSession $session): View
    {
        return view('live.admin.show', [
            'session' => $session,
            'questions' => $session->questions()->withCount('answers')->get(),
        ]);
    }

    /** Vue presentateur : question en cours avec ses resultats, notes, liste des questions. */
    public function presenter(LiveSession $session): View
    {
        return view('live.admin.presenter', [
            'session' => $session,
            'questions' => $session->questions()->withCount('answers')->get(),
        ]);
    }

    /** Compteurs pour les pages de pilotage, rafraichis toutes les quelques secondes. */
    public function state(LiveSession $session): JsonResponse
    {
        $current = $session->current_question_id ? LiveQuestion::find($session->current_question_id) : null;

        return response()->json([
            'is_active' => $session->is_active,
            'current_question_id' => $session->current_question_id,
            'questions' => $session->questions()->withCount('answers')->get()
                ->map(fn ($q) => ['id' => $q->id, 'status' => $q->status, 'show_results' => $q->show_results, 'answers' => $q->answers_count])
                ->all(),
            // L'animateur voit toujours les resultats, meme quand la salle ne les voit pas encore.
            'current' => $current ? array_merge($current->toPublicArray(), ['notes' => $current->notes, 'results' => $current->results()]) : null,
        ]);
    }

    public function activate(LiveSession $session): RedirectResponse
    {
        if ($session->is_active) {
            $session->update(['is_active' => false]);

            return back()->with('status', 'Sondage mis en pause : /live affiche « pas d\'atelier en cours ».');
        }

        $session->activate();

        return back()->with('status', "Sondage en ligne sur /live avec le code {$session->code}.");
    }

    /** Ecran d'attente : plus de question en cours, l'ecran montre le code. */
    public function lobby(LiveSession $session): RedirectResponse
    {
        $session->update(['current_question_id' => null]);

        return back();
    }

    public function open(LiveSession $session, LiveQuestion $question): RedirectResponse
    {
        $this->ensureBelongs($session, $question);

        // Une seule question ouverte a la fois : celle qui l'etait se ferme.
        $session->questions()->where('status', LiveQuestion::STATUS_OPEN)->where('id', '!=', $question->id)
            ->update(['status' => LiveQuestion::STATUS_CLOSED]);

        $question->update(['status' => LiveQuestion::STATUS_OPEN]);
        $session->update(['current_question_id' => $question->id]);

        return back();
    }

    public function toggleResults(LiveSession $session, LiveQuestion $question): RedirectResponse
    {
        $this->ensureBelongs($session, $question);
        $question->update(['show_results' => ! $question->show_results]);
        if ($session->current_question_id !== $question->id) {
            $session->update(['current_question_id' => $question->id]);
        }

        return back();
    }

    public function close(LiveSession $session, LiveQuestion $question): RedirectResponse
    {
        $this->ensureBelongs($session, $question);
        $question->update(['status' => LiveQuestion::STATUS_CLOSED]);

        return back();
    }

    /** Efface les reponses et remet la question a zero (repetition, test en salle). */
    public function reset(LiveSession $session, LiveQuestion $question): RedirectResponse
    {
        $this->ensureBelongs($session, $question);
        $question->answers()->delete();
        $question->update(['status' => LiveQuestion::STATUS_PENDING, 'show_results' => false]);

        return back()->with('status', 'Réponses effacées.');
    }

    public function storeQuestion(Request $request, LiveSession $session): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', 'in:'.implode(',', LiveQuestion::TYPES)],
            'prompt' => ['required', 'string', 'max:500'],
            'options' => ['nullable', 'string', 'max:2000'],
        ]);

        $options = null;
        if ($data['type'] === LiveQuestion::TYPE_CHOICE) {
            $options = collect(preg_split('/\r?\n/', $data['options'] ?? ''))->map(fn ($o) => trim($o))->filter()->values()->all();
            if (count($options) < 2) {
                return back()->withInput()->withErrors(['options' => 'Il faut au moins deux options, une par ligne.']);
            }
        }

        $session->questions()->create([
            'position' => ((int) $session->questions()->max('position')) + 1,
            'type' => $data['type'],
            'prompt' => $data['prompt'],
            'options' => $options,
        ]);

        return back()->with('status', 'Question ajoutée en fin de liste.');
    }

    public function destroyQuestion(LiveSession $session, LiveQuestion $question): RedirectResponse
    {
        $this->ensureBelongs($session, $question);
        if ($session->current_question_id === $question->id) {
            $session->update(['current_question_id' => null]);
        }
        $question->delete();

        return back()->with('status', 'Question supprimée.');
    }

    public function destroy(LiveSession $session): RedirectResponse
    {
        $session->delete();

        return redirect()->route('admin.live.index')->with('status', 'Sondage supprimé avec toutes ses réponses.');
    }

    /** Export CSV de toutes les reponses, pour le message de suivi apres l'atelier. */
    public function export(LiveSession $session): StreamedResponse
    {
        $filename = 'sondage-'.Str::slug($session->title).'-'.now()->format('Ymd-Hi').'.csv';

        return response()->streamDownload(function () use ($session) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['question', 'type', 'reponse', 'horodatage'], ';');
            foreach ($session->questions as $question) {
                $options = array_values($question->options ?? []);
                foreach ($question->answers()->orderBy('created_at')->get() as $answer) {
                    $value = $answer->option_index !== null ? ($options[$answer->option_index] ?? $answer->option_index) : $answer->value;
                    fputcsv($out, [$question->prompt, $question->typeLabel(), $value, $answer->created_at->format('Y-m-d H:i:s')], ';');
                }
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function ensureBelongs(LiveSession $session, LiveQuestion $question): void
    {
        abort_unless($question->live_session_id === $session->id, 404);
    }
}
