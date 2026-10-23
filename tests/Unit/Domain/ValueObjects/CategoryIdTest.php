<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\ValueObjects;

use App\Domain\ValueObjects\CategoryId;
use App\Tests\Unit\UnitTestCase;
use InvalidArgumentException;
use Symfony\Component\Uid\Uuid;

final class CategoryIdTest extends UnitTestCase
{
    public function testItHoldsValidUuid(): void
    {
        $uuid = Uuid::v7()->toRfc4122();

        self::assertSame($uuid, (new CategoryId($uuid))->value);
    }

    public function testInvalidUuidIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new CategoryId('not-a-uuid');
    }

    public function testRandomGeneratesValidUuid(): void
    {
        self::assertTrue(Uuid::isValid(CategoryId::random()->value));
    }
}