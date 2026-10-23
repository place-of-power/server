<?php

declare(strict_types=1);

namespace App\Application\Query\ListCategories;

use App\Domain\Contracts\Repository\CategoryRepository;
use App\Domain\Entity\Category;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ListCategoriesHandler
{
    public function __construct(private CategoryRepository $categoriesRepository)
    {
    }

    /**
     * @return list<Category>
     */
    public function __invoke(ListCategoriesQuery $query): array
    {
        return $this->categoriesRepository->getOrdered($query->limit);
    }
}