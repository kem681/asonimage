<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'edition_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class);
    }

    public function canAccessEdition(Edition $edition): bool
    {
        return $this->is_admin || $edition->is_common || $this->edition_id === $edition->id;
    }

    // ------------------------------------------------------------------
    // Module 3x30
    // ------------------------------------------------------------------

    public function workshopParticipant(): HasOne
    {
        return $this->hasOne(WorkshopParticipant::class);
    }

    public function diagnostics(): HasMany
    {
        return $this->hasMany(Diagnostic::class);
    }

    public function anchors(): HasMany
    {
        return $this->hasMany(Anchor::class);
    }

    public function workshopGroups(): BelongsToMany
    {
        return $this->belongsToMany(WorkshopGroup::class, 'workshop_group_members')
            ->withPivot('joined_at')
            ->withTimestamps()
            ->orderBy('name');
    }

    public function isWorkshopParticipant(): bool
    {
        return $this->workshopParticipant()->exists();
    }

    public function canAccessWorkshop(): bool
    {
        return $this->is_admin || $this->isWorkshopParticipant();
    }

    public function isSeminarMember(): bool
    {
        return $this->edition_id !== null;
    }

    public function latestDiagnostic(): ?Diagnostic
    {
        return $this->diagnostics()->orderByDesc('completed_at')->orderByDesc('id')->first();
    }

    public function activeAnchor(): ?Anchor
    {
        return $this->anchors()->where('is_active', true)->orderByDesc('id')->first();
    }

    public function firstName(): string
    {
        $parts = preg_split('/\s+/', trim((string) $this->name)) ?: [];

        return $parts[0] ?? (string) $this->name;
    }
}
