<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['email', 'name', 'edition_id', 'source'])]
class AuthorizedEmail extends Model
{
    const SOURCE_INSCRIPTION = 'inscription';

    const SOURCE_IMPORT_CSV = 'import_csv';

    const SOURCE_ADMIN_MANUEL = 'admin_manuel';

    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class);
    }
}
