<?php

declare(strict_types=1);

namespace App\Application\Query\GetCategoryArticles;

use App\Domain\ValueObjects\ArticleCursor;

final readonly class GetCategoryArticlesQuery
{
    public function __construct(
        public string $categorySlug,
        public int $limit,
        public ?ArticleCursor $cursor,
    ) {
    }
}