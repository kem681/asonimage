<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LiveAnswer extends Model
{
    protected $fillable = ['live_question_id', 'token', 'option_index', 'value'];

    public function question(): BelongsTo
    {
        return $this->belongsTo(LiveQuestion::class, 'live_question_id');
    }
}
