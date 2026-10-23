<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\ValueObjects;

use App\Domain\ValueObjects\ArticleId;
use App\Tests\Unit\UnitTestCase;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

final class ArticleIdTest extends UnitTestCase
{
    public function testItHoldsValidUuid(): void
    {
        $uuid = Uuid::v7()->toRfc4122();

        self::assertSame($uuid, (new ArticleId($uuid))->value);
    }

    public function testInvalidUuidIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new ArticleId('nope');
    }

    public function testRandomGeneratesValidUuid(): void
    {
        self::assertTrue(Uuid::isValid(ArticleId::random()->value));
    }
}