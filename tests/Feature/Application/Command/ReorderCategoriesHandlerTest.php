<?php

declare(strict_types=1);

namespace App\Tests\Feature\Application\Command;

use App\Application\Command\ReorderCategories\ReorderCategoriesCommand;
use App\Domain\Entity\Category;
use App\Tests\Factory\CategoryFactory;
use App\Tests\Feature\HandlerTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final class ReorderCategoriesHandlerTest extends HandlerTestCase
{
    public function testAppliesProvidedSortOrders(): void
    {
        $order = [
            ['name' => 'PHP', 'slug' => 'php', 'sortOrder' => 30],
            ['name' => 'СУБД', 'slug' => 'databases', 'sortOrder' => 10],
            ['name' => 'Архитектура', 'slug' => 'architecture', 'sortOrder' => 20],
        ];

        $items = array_map(
            fn (array $o): array => ['id' => $this->seed($o['name'], $o['slug']), 'sortOrder' => $o['sortOrder']],
            $order,
        );

        $this->messageBus->dispatch(new ReorderCategoriesCommand($items));

        self::assertSame(
            ['СУБД', 'Архитектура', 'PHP'],
            array_map(static fn (Category $c): string => $c->name(), $this->ordered()),
        );
    }

    public function testMissingCategoryThrowsNotFound(): void
    {
        $this->seed('PHP', 'php');

        try {
            $this->messageBus->dispatch(new ReorderCategoriesCommand([
                ['id' => '00000000-0000-0000-0000-000000000000', 'sortOrder' => 0],
            ]));
            self::fail('Dispatcher should have thrown.');
        } catch (HandlerFailedException $e) {
            self::assertInstanceOf(NotFoundHttpException::class, $e->getPrevious());
        }
    }

    private function seed(string $name, string $slug): string
    {
        return CategoryFactory::create($this->entityManager, $name, $slug)->id()->value;
    }

    /**
     * @return list<Category>
     */
    private function ordered(): array
    {
        return $this->entityManager->getRepository(Category::class)
            ->findBy([], ['sortOrder' => 'ASC']);
    }
}