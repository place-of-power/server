<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories;

use App\Domain\Entity\Article;
use App\Domain\ValueObjects\ArticleId;
use App\Domain\ValueObjects\Slug;

interface ArticleRepository
{
    public function save(Article $article): void;

    public function remove(Article $article): void;

    public function findById(ArticleId $id): ?Article;

    public function findBySlug(Slug $slug): ?Article;

    /**
     * @return list<Article>
     */
    public function findPublished(?string $categorySlug, ?string $q, int $limit, int $offset): array;
}
