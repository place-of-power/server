<?php

declare(strict_types=1);

namespace App\Application\Query\ListCategories;

final readonly class ListCategoriesQuery
{
    public function __construct(public ?int $limit)
    {
    }
}