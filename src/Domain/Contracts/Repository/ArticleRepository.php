<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repository;

use App\Domain\Entity\Article;

interface ArticleRepository
{
    /**
     * @return list<Article>
     */
    public function getRecent(int $limit): array;
}