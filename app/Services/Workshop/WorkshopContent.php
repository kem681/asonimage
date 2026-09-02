<?php

namespace App\Services\Workshop;

use InvalidArgumentException;

/**
 * Acces au contenu du modele (resources/content/3x30.php) : axes,
 * affirmations, echelle, textes des ecrans. Le fichier est charge une fois.
 */
class WorkshopContent
{
    /** @var array<string, mixed> */
    private array $content;

    public function __construct(?string $path = null)
    {
        $path ??= resource_path('content/3x30.php');

        $this->content = require $path;
    }

    /** @return array<string, array<string, mixed>> */
    public function axes(): array
    {
        return $this->content['axes'];
    }

    /** @return list<string> */
    public function axisKeys(): array
    {
        return array_keys($this->content['axes']);
    }

    public function hasAxis(string $key): bool
    {
        return array_key_exists($key, $this->content['axes']);
    }

    /** @return array<string, mixed> */
    public function axis(string $key): array
    {
        if (! $this->hasAxis($key)) {
            throw new InvalidArgumentException("Axe inconnu : {$key}");
        }

        return $this->content['axes'][$key];
    }

    public function axisLabel(string $key): string
    {
        return $this->axis($key)['label'];
    }

    /** @return list<array{axis: string, text: string}> */
    public function statements(): array
    {
        return $this->content['statements'];
    }

    public function statementCount(): int
    {
        return count($this->content['statements']);
    }

    /** @return array<int, string> */
    public function scale(): array
    {
        return $this->content['scale'];
    }

    public function scaleMin(): int
    {
        return min(array_keys($this->content['scale']));
    }

    public function scaleMax(): int
    {
        return max(array_keys($this->content['scale']));
    }

    public function text(string $key): string
    {
        return $this->content['texts'][$key] ?? '';
    }
}
