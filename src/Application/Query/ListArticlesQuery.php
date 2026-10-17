<?php

declare(strict_types=1);

namespace App\Application\Query;

use App\Domain\Types\ArticleStatus;

final readonly class ListArticlesQuery
{
    public function __construct(
        public ?string $categorySlug = null,
        public ?ArticleStatus $status = null,
        public ?string $q = null,
        public int $limit = 20,
        public int $offset = 0,
    ) {
    }
}