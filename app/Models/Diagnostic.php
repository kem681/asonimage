<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['user_id', 'answers', 'score_filiation', 'score_desert', 'score_appel', 'axis', 'completed_at'])]
class Diagnostic extends Model
{
    protected function casts(): array
    {
        return [
            'answers' => 'array',
            'completed_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** @return array<string, int> */
    public function scores(): array
    {
        return [
            'filiation' => (int) $this->score_filiation,
            'desert' => (int) $this->score_desert,
            'appel' => (int) $this->score_appel,
        ];
    }

    /** Axes dont le manquement est le plus fort (plusieurs en cas d'egalite). */
    public function leadingAxes(): array
    {
        $scores = $this->scores();
        $max = max($scores);

        return array_keys(array_filter($scores, fn (int $score) => $score === $max));
    }

    public function hasAxis(): bool
    {
        return $this->axis !== null;
    }
}
