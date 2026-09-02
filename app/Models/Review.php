<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['anchor_id', 'held', 'changed', 'next_friction', 'decision', 'reviewed_on'])]
class Review extends Model
{
    const HELD_YES = 'oui';

    const HELD_PARTLY = 'partie';

    const HELD_NO = 'non';

    const HELD_LABELS = [
        self::HELD_YES => 'Oui',
        self::HELD_PARTLY => 'En partie',
        self::HELD_NO => 'Non',
    ];

    const DECISION_CONTINUE = 'continuer';

    const DECISION_NEW_GESTURE = 'nouveau';

    const DECISION_DIAGNOSTIC = 'diagnostic';

    const DECISIONS = [
        self::DECISION_CONTINUE,
        self::DECISION_NEW_GESTURE,
        self::DECISION_DIAGNOSTIC,
    ];

    protected function casts(): array
    {
        return [
            'reviewed_on' => 'immutable_date',
        ];
    }

    public function anchor(): BelongsTo
    {
        return $this->belongsTo(Anchor::class);
    }

    public function heldLabel(): string
    {
        return self::HELD_LABELS[$this->held] ?? $this->held;
    }
}
