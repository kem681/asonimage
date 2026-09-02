<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['code', 'label', 'workshop_type', 'is_active'])]
class WorkshopCode extends Model
{
    const TYPE_3X30 = '3x30';

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /** Le code est toujours stocke normalise (majuscules, sans espaces ni tirets). */
    protected function code(): Attribute
    {
        return Attribute::make(set: fn (string $value) => static::normalize($value));
    }

    public function participants(): HasMany
    {
        return $this->hasMany(WorkshopParticipant::class);
    }

    public static function normalize(string $code): string
    {
        return strtoupper(trim(str_replace([' ', '-'], '', $code)));
    }

    public static function findActive(string $code): ?self
    {
        return static::query()
            ->where('code', static::normalize($code))
            ->where('is_active', true)
            ->first();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
