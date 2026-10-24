<?php

declare(strict_types=1);

namespace App\Application\Command\ReorderCategories;

use App\Domain\Contracts\Repository\CategoryRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ReorderCategoriesHandler
{
    public function __construct(
        private CategoryRepository $categoriesRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(ReorderCategoriesCommand $command): void
    {
        $ids = array_map(static fn (array $item): string => $item['id'], $command->items);
        $categories = $this->categoriesRepository->getByIds($ids);

        if (count($categories) !== count($ids)) {
            throw new NotFoundHttpException('Одна или несколько категорий не найдены.');
        }

        $sortOrders = [];
        foreach ($command->items as $item) {
            $sortOrders[$item['id']] = $item['sortOrder'];
        }

        $now = new DateTimeImmutable();
        foreach ($categories as $category) {
            $category->changeSortOrder($sortOrders[$category->id()->value], $now);
        }

        $this->entityManager->flush();
    }
}