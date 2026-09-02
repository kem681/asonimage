<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['anchor_id', 'week_start', 'body', 'told_to', 'told_on'])]
class Friction extends Model
{
    protected function casts(): array
    {
        return [
            'week_start' => 'immutable_date',
            'told_on' => 'immutable_date',
        ];
    }

    public function anchor(): BelongsTo
    {
        return $this->belongsTo(Anchor::class);
    }

    public function isTold(): bool
    {
        return $this->told_on !== null;
    }
}
