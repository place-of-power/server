<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\ValueObjects;

use App\Domain\ValueObjects\Slug;
use App\Tests\Unit\UnitTestCase;
use InvalidArgumentException;

final class SlugTest extends UnitTestCase
{
    public function testItHoldsValue(): void
    {
        self::assertSame('architecture', (new Slug('architecture'))->value);
    }

    public function testEmptySlugIsRejected(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Slug('');
    }

    public function testItStringifies(): void
    {
        self::assertSame('php', (string) new Slug('php'));
    }
}