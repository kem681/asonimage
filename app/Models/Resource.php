<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['title', 'description', 'file_path', 'original_filename', 'edition_id', 'day'])]
class Resource extends Model
{
    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class);
    }
}
