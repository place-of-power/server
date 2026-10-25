<?php

declare(strict_types=1);

namespace App\Application\Query\GetArticle;

final readonly class GetArticleQuery
{
    public function __construct(public string $slug)
    {
    }
}