<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['title', 'description', 'file_path', 'original_filename', 'edition_id', 'day'])]
class Resource extends Model
{
    const AUDIO_EXTENSIONS = ['mp3', 'wav', 'ogg', 'm4a'];

    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class);
    }

    public function isAudio(): bool
    {
        return in_array($this->extension(), self::AUDIO_EXTENSIONS, true);
    }

    public function extension(): string
    {
        return strtolower(pathinfo($this->original_filename, PATHINFO_EXTENSION));
    }
}
