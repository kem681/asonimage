<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LiveQuestion extends Model
{
    public const TYPE_CHOICE = 'choice';

    public const TYPE_WORDS = 'words';

    public const TYPE_TEXT = 'text';

    public const TYPES = [self::TYPE_CHOICE, self::TYPE_WORDS, self::TYPE_TEXT];

    public const STATUS_PENDING = 'pending';

    public const STATUS_OPEN = 'open';

    public const STATUS_CLOSED = 'closed';

    protected $fillable = ['live_session_id', 'position', 'type', 'prompt', 'options', 'notes', 'status', 'show_results'];

    protected function casts(): array
    {
        return ['options' => 'array', 'show_results' => 'boolean'];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(LiveSession::class, 'live_session_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(LiveAnswer::class);
    }

    public function isOpen(): bool
    {
        return $this->status === self::STATUS_OPEN;
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            self::TYPE_CHOICE => 'Choix unique',
            self::TYPE_WORDS => 'Nuage de mots',
            self::TYPE_TEXT => 'Texte libre',
            default => $this->type,
        };
    }

    /** Ce que voient participants et ecran : la question sans les reponses. */
    public function toPublicArray(): array
    {
        return [
            'id' => $this->id,
            'position' => $this->position,
            'type' => $this->type,
            'prompt' => $this->prompt,
            'options' => $this->type === self::TYPE_CHOICE ? array_values($this->options ?? []) : null,
            'status' => $this->status,
            'show_results' => $this->show_results,
        ];
    }

    /** Agregat des reponses selon le type. Calcule a la demande, la volumetrie d'une salle reste petite. */
    public function results(): array
    {
        $answers = $this->answers()->get(['option_index', 'value', 'created_at']);
        $total = $answers->count();

        return match ($this->type) {
            self::TYPE_CHOICE => [
                'total' => $total,
                'items' => collect($this->options ?? [])->values()->map(function ($label, $index) use ($answers, $total) {
                    $count = $answers->where('option_index', $index)->count();

                    return ['label' => $label, 'count' => $count, 'pct' => $total ? (int) round($count * 100 / $total) : 0];
                })->all(),
            ],
            self::TYPE_WORDS => [
                'total' => $total,
                'items' => $answers->pluck('value')->filter()
                    ->map(fn ($v) => Str::lower(trim($v)))
                    ->countBy()
                    ->sortDesc()
                    ->take(60)
                    ->map(fn ($count, $word) => ['word' => $word, 'count' => $count])
                    ->values()->all(),
            ],
            default => [
                'total' => $total,
                'items' => $answers->sortByDesc('created_at')->pluck('value')->filter()->values()->all(),
            ],
        };
    }

    /** Nettoie une reponse selon le type, ou renvoie null si elle n'est pas acceptable. */
    public function cleanValue(?string $value): ?string
    {
        $value = trim(preg_replace('/\s+/u', ' ', (string) $value));
        if ($value === '') {
            return null;
        }

        return match ($this->type) {
            self::TYPE_WORDS => Str::limit($value, 40, ''),
            default => Str::limit($value, 400, ''),
        };
    }
}
