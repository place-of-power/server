<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

final readonly class CategoryId
{
    public function __construct(public string $value)
    {
        if (!Uuid::isValid($value)) {
            throw new InvalidArgumentException(sprintf('"%s" is not a valid UUID', $value));
        }
    }

    public static function random(): self
    {
        return new self(Uuid::v7()->toRfc4122());
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
