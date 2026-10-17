<?php

declare(strict_types=1);

namespace App\Application\Query\Dto;

final readonly class ArticleDto
{
    public function __construct(
        public string $id,
        public string $slug,
        public string $title,
        public string $description,
        public string $body,
        public string $categorySlug,
        public string $categoryName,
        public ?string $publishedAt,
        public ?string $readingTime,
    ) {
    }
}