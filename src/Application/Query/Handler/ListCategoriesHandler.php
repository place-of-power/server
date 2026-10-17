<?php

declare(strict_types=1);

namespace App\Application\Query\Handler;

use App\Application\Query\Dto\CategoryDto;
use App\Application\Query\ListCategoriesQuery;
use App\Domain\Contracts\Repositories\CategoryRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ListCategoriesHandler
{
    public function __construct(private CategoryRepository $categoryRepository)
    {
    }

    /**
     * @return list<CategoryDto>
     */
    public function __invoke(ListCategoriesQuery $query): array
    {
        $categories = $this->categoryRepository->findAll();

        return array_map(
            static fn ($category) => new CategoryDto(
                id: $category->id()->value,
                name: $category->name(),
                slug: $category->slug()->value,
            ),
            $categories,
        );
    }
}