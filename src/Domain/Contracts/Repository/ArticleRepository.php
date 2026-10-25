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

    /**
     * @param array{publishedAt: \DateTimeImmutable, id: string} $cursor
     *
     * @return array{items: list<Article>, hasMore: bool}
     */
    public function getFromCategoryWithCursor(string $categoryId, int $limit, ?array $cursor): array;

    public function findBySlug(string $slug): ?Article;
}