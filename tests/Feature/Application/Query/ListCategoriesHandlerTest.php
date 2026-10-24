<?php

declare(strict_types=1);

namespace App\Tests\Feature\Application\Query;

use App\Application\Query\ListCategories\ListCategoriesQuery;
use App\Domain\Entity\Category;
use App\Tests\Factory\CategoryFactory;
use App\Tests\Feature\HandlerTestCase;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class ListCategoriesHandlerTest extends HandlerTestCase
{
    public function testReturnsCategoriesOrderedBySortOrderWithoutLimit(): void
    {
        CategoryFactory::create($this->entityManager, 'СУБД', 'databases', 30);
        CategoryFactory::create($this->entityManager, 'PHP', 'php', 10);
        CategoryFactory::create($this->entityManager, 'Архитектура', 'architecture', 20);

        $envelope = $this->messageBus->dispatch(new ListCategoriesQuery(null));
        /** @var list<Category> $categories */
        $categories = $envelope->last(HandledStamp::class)->getResult();

        self::assertCount(3, $categories);
        self::assertSame(['PHP', 'Архитектура', 'СУБД'], array_map(
            static fn (Category $c): string => $c->name(),
            $categories,
        ));
    }

    public function testLimitRestrictsCount(): void
    {
        CategoryFactory::create($this->entityManager, 'PHP', 'php', 10);
        CategoryFactory::create($this->entityManager, 'СУБД', 'databases', 20);
        CategoryFactory::create($this->entityManager, 'Архитектура', 'architecture', 30);

        $envelope = $this->messageBus->dispatch(new ListCategoriesQuery(2));
        /** @var list<Category> $categories */
        $categories = $envelope->last(HandledStamp::class)->getResult();

        self::assertCount(2, $categories);
        self::assertSame(['PHP', 'СУБД'], array_map(
            static fn (Category $c): string => $c->name(),
            $categories,
        ));
    }
}