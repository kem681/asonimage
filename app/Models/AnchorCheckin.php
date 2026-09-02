<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['anchor_id', 'day', 'held'])]
class AnchorCheckin extends Model
{
    protected function casts(): array
    {
        return [
            'day' => 'immutable_date',
            'held' => 'boolean',
        ];
    }

    public function anchor(): BelongsTo
    {
        return $this->belongsTo(Anchor::class);
    }
}
