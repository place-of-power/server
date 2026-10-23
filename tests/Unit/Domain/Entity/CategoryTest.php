<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\Category;
use App\Domain\ValueObjects\CategoryId;
use App\Domain\ValueObjects\Slug;
use App\Tests\Unit\UnitTestCase;
use DateTimeImmutable;

final class CategoryTest extends UnitTestCase
{
    public function testCreateSetsFields(): void
    {
        $now = new DateTimeImmutable('2026-10-23 12:00:00');

        $category = Category::create(
            CategoryId::random(),
            'Архитектура',
            new Slug('architecture'),
            10,
            $now,
        );

        self::assertSame('Архитектура', $category->name());
        self::assertSame('architecture', $category->slug()->value);
        self::assertSame(10, $category->sortOrder());
        self::assertSame($now, $category->createdAt());
        self::assertSame($now, $category->updatedAt());
    }

    public function testDefaultSortOrderIsZero(): void
    {
        $category = Category::create(
            CategoryId::random(),
            'PHP',
            new Slug('php'),
        );

        self::assertSame(0, $category->sortOrder());
    }
}