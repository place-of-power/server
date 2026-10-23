<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\ValueObjects;

use App\Domain\Types\ArticleStatus;
use App\Tests\Unit\UnitTestCase;

final class ArticleStatusTest extends UnitTestCase
{
    public function testKnownStatuses(): void
    {
        self::assertSame('draft', ArticleStatus::Draft->value);
        self::assertSame('published', ArticleStatus::Published->value);
        self::assertSame('archived', ArticleStatus::Archived->value);
    }

    public function testFromString(): void
    {
        self::assertSame(ArticleStatus::Published, ArticleStatus::from('published'));
    }
}