<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use DateTimeImmutable;
use InvalidArgumentException;

final readonly class ArticleCursor
{
    public function __construct(
        public DateTimeImmutable $publishedAt,
        public string $id,
    ) {
    }

    public static function fromString(?string $cursor): ?self
    {
        if ($cursor === null || $cursor === '') {
            return null;
        }

        $decoded = base64_decode($cursor, true);

        if ($decoded === false) {
            throw new InvalidArgumentException('Неверный курсор пагинации.');
        }

        [$at, $id] = array_pad(explode('|', $decoded, 2), 2, null);

        if ($at === null || $id === null) {
            throw new InvalidArgumentException('Неверный курсор пагинации.');
        }

        return new self(new DateTimeImmutable($at), $id);
    }

    public function encode(): string
    {
        return base64_encode($this->publishedAt->format('Y-m-d\TH:i:sP').'|'.$this->id);
    }
}