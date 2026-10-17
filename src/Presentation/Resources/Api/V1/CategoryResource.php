<?php

declare(strict_types=1);

namespace App\Presentation\Resources\Api\V1;

use App\Domain\Entity\Category;

final readonly class CategoryResource
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
    ) {
    }

    public static function fromEntity(Category $category): self
    {
        return new self(
            $category->id()->value,
            $category->name(),
            $category->slug()->value,
        );
    }
}
