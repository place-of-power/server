<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final readonly class Slug
{
    public function __construct(public string $value)
    {
        if ('' === $value) {
            throw new InvalidArgumentException('Slug cannot be empty');
        }
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
