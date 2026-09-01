<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['year', 'label'])]
class Edition extends Model
{
    public function authorizedEmails(): HasMany
    {
        return $this->hasMany(AuthorizedEmail::class);
    }

    public function resources(): HasMany
    {
        return $this->hasMany(Resource::class);
    }
}
