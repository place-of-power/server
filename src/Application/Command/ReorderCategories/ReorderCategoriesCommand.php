<?php

declare(strict_types=1);

namespace App\Application\Command\ReorderCategories;

final readonly class ReorderCategoriesCommand
{
    /**
     * @param list<array{id: string, sortOrder: int}> $items
     */
    public function __construct(public array $items)
    {
    }
}