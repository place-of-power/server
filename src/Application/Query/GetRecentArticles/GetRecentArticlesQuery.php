<?php

declare(strict_types=1);

namespace App\Application\Query\GetRecentArticles;

final readonly class GetRecentArticlesQuery
{
    public function __construct(public int $limit)
    {
    }
}