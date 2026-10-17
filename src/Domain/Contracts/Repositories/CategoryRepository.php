<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories;

use App\Domain\Entity\Category;
use App\Domain\ValueObjects\CategoryId;
use App\Domain\ValueObjects\Slug;

interface CategoryRepository
{
    public function save(Category $category): void;

    public function findById(CategoryId $id): ?Category;

    public function findBySlug(Slug $slug): ?Category;

    /**
     * @return list<Category>
     */
    public function findAll(): array;
}
