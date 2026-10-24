<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repository;

use App\Domain\Entity\Category;

interface CategoryRepository
{
    /**
     * @return list<Category>
     */
    public function getOrdered(?int $limit): array;

    /**
     * @param list<string> $ids
     *
     * @return list<Category>
     */
    public function getByIds(array $ids): array;
}