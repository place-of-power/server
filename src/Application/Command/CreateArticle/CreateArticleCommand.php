<?php

declare(strict_types=1);

namespace App\Application\Command\CreateArticle;

final readonly class CreateArticleCommand
{
    public function __construct(
        public string $slug,
        public string $title,
        public string $description,
        public string $body,
        public string $status,
        public string $categoryId,
    ) {
    }
}