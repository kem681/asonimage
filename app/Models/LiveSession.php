<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LiveSession extends Model
{
    protected $fillable = ['code', 'title', 'is_active', 'current_question_id'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function questions(): HasMany
    {
        return $this->hasMany(LiveQuestion::class)->orderBy('position');
    }

    public function currentQuestion(): BelongsTo
    {
        return $this->belongsTo(LiveQuestion::class, 'current_question_id');
    }

    public static function active(): ?self
    {
        return static::where('is_active', true)->latest('updated_at')->first();
    }

    /** Code court, lisible a l'ecran : lettres et chiffres sans ambiguite (pas de O/0, I/1). */
    public static function generateCode(): string
    {
        $alphabet = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        do {
            $code = '';
            for ($i = 0; $i < 4; $i++) {
                $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
            }
        } while (static::where('code', $code)->exists());

        return $code;
    }

    /** Active cette session et desactive toutes les autres : une seule salle a la fois sur /live. */
    public function activate(): void
    {
        static::where('id', '!=', $this->id)->update(['is_active' => false]);
        $this->update(['is_active' => true]);
    }

    /** Cree une session a partir d'un modele de resources/content/live/*.php */
    public static function createFromTemplate(string $template): self
    {
        $path = resource_path("content/live/{$template}.php");
        abort_unless(is_file($path), 404);
        $definition = require $path;

        $session = static::create([
            'code' => static::generateCode(),
            'title' => $definition['title'],
            'is_active' => false,
        ]);

        foreach ($definition['questions'] as $i => $question) {
            $session->questions()->create([
                'position' => $i + 1,
                'type' => $question['type'],
                'prompt' => $question['prompt'],
                'options' => $question['options'] ?? null,
                'notes' => $question['notes'] ?? null,
            ]);
        }

        return $session;
    }

    public static function templates(): array
    {
        $templates = [];
        foreach (glob(resource_path('content/live/*.php')) ?: [] as $file) {
            $definition = require $file;
            $templates[basename($file, '.php')] = $definition['title'];
        }

        return $templates;
    }

    public function joinUrl(): string
    {
        return rtrim(config('app.url'), '/').'/live';
    }

    public function shortJoinUrl(): string
    {
        return Str::of($this->joinUrl())->replace(['https://', 'http://', 'www.'], '')->toString();
    }
}
