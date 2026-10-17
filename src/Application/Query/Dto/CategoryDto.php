<?php

declare(strict_types=1);

namespace App\Application\Query\Dto;

final readonly class CategoryDto
{
    public function __construct(
        public string $id,
        public string $name,
        public string $slug,
    ) {
    }
}