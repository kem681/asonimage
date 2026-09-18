<?php

namespace App\Http\Controllers\Live;

use App\Http\Controllers\Controller;
use App\Models\LiveAnswer;
use App\Models\LiveQuestion;
use App\Models\LiveSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Page participant : asonimage.ch/live. Aucun compte, aucun nom.
 * Un jeton anonyme en cookie garantit une reponse par telephone et par question.
 */
class ParticipantController extends Controller
{
    public const COOKIE = 'live_token';

    public function show(Request $request): View
    {
        return view('live.participant', [
            'session' => LiveSession::active(),
            'token' => $this->token($request),
        ]);
    }

    /** Etat courant, interroge toutes les quelques secondes par la page. Doit rester leger. */
    public function state(Request $request): JsonResponse
    {
        $session = LiveSession::active();
        if (! $session) {
            return response()->json(['session' => null, 'question' => null]);
        }

        $question = $session->current_question_id ? LiveQuestion::find($session->current_question_id) : null;
        $payload = [
            'session' => ['code' => $session->code, 'title' => $session->title],
            'question' => $question?->toPublicArray(),
            'answer' => null,
            'results' => null,
        ];

        if ($question) {
            $mine = LiveAnswer::where('live_question_id', $question->id)->where('token', $this->token($request))->first();
            $payload['answer'] = $mine ? ['option_index' => $mine->option_index, 'value' => $mine->value] : null;
            if ($question->show_results) {
                $payload['results'] = $question->results();
            }
        }

        return response()->json($payload);
    }

    public function answer(Request $request): JsonResponse
    {
        $data = $request->validate([
            'question_id' => ['required', 'integer'],
            'option_index' => ['nullable', 'integer', 'min:0', 'max:20'],
            'value' => ['nullable', 'string', 'max:400'],
        ]);

        $session = LiveSession::active();
        $question = $session ? LiveQuestion::where('live_session_id', $session->id)->find($data['question_id']) : null;

        if (! $question || ! $question->isOpen() || $session->current_question_id !== $question->id) {
            return response()->json(['ok' => false, 'reason' => 'closed'], 409);
        }

        $attributes = ['option_index' => null, 'value' => null];

        if ($question->type === LiveQuestion::TYPE_CHOICE) {
            $index = $data['option_index'] ?? null;
            if ($index === null || ! array_key_exists($index, array_values($question->options ?? []))) {
                return response()->json(['ok' => false, 'reason' => 'invalid'], 422);
            }
            $attributes['option_index'] = $index;
        } else {
            $value = $question->cleanValue($data['value'] ?? null);
            if ($value === null) {
                return response()->json(['ok' => false, 'reason' => 'empty'], 422);
            }
            $attributes['value'] = $value;
        }

        LiveAnswer::updateOrCreate(
            ['live_question_id' => $question->id, 'token' => $this->token($request)],
            $attributes,
        );

        return response()->json(['ok' => true]);
    }

    /** Jeton anonyme, pose en cookie pour un an. Jamais relie a un compte. */
    private function token(Request $request): string
    {
        $token = $request->cookie(self::COOKIE);
        if (! is_string($token) || strlen($token) < 20 || strlen($token) > 64) {
            $token = Str::random(40);
            Cookie::queue(Cookie::make(self::COOKIE, $token, 60 * 24 * 365, null, null, null, true, false, 'Lax'));
        }

        return $token;
    }
}
