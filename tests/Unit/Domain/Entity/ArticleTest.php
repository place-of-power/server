<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Entity;

use App\Domain\Entity\Article;
use App\Domain\Entity\Category;
use App\Domain\Types\ArticleStatus;
use App\Domain\ValueObjects\ArticleId;
use App\Domain\ValueObjects\CategoryId;
use App\Domain\ValueObjects\Slug;
use App\Tests\Unit\UnitTestCase;
use DateTimeImmutable;

final class ArticleTest extends UnitTestCase
{
    public function testCreateDraftHasNoPublishedAt(): void
    {
        $now = new DateTimeImmutable('2026-10-23 12:00:00');
        $category = $this->category();
        $article = $this->createArticle($category, ArticleStatus::Draft, $now);

        self::assertSame(ArticleStatus::Draft, $article->status());
        self::assertSame($category, $article->category());
        self::assertSame($category->id()->value, $article->categoryId()->value);
        self::assertNull($article->publishedAt());
        self::assertSame($now, $article->createdAt());
        self::assertSame($now, $article->updatedAt());
    }

    public function testCreatePublishedSetsPublishedAt(): void
    {
        $now = new DateTimeImmutable('2026-10-23 12:00:00');
        $article = $this->createArticle($this->category(), ArticleStatus::Published, $now);

        self::assertSame($now, $article->publishedAt());
    }

    public function testPublishSetsStatusAndTimestamps(): void
    {
        $created = new DateTimeImmutable('2026-10-20 09:00:00');
        $article = $this->createArticle($this->category(), ArticleStatus::Draft, $created);

        $publishAt = new DateTimeImmutable('2026-10-23 15:30:00');
        $article->publish($publishAt);

        self::assertSame(ArticleStatus::Published, $article->status());
        self::assertSame($publishAt, $article->publishedAt());
        self::assertSame($publishAt, $article->updatedAt());
        self::assertSame($created, $article->createdAt());
    }

    private function createArticle(Category $category, ArticleStatus $status, DateTimeImmutable $now): Article
    {
        return Article::create(
            ArticleId::random(),
            new Slug('article-slug'),
            'Title',
            'Description',
            'Body',
            $status,
            $category,
            $now,
        );
    }

    private function category(): Category
    {
        return Category::create(
            CategoryId::random(),
            'PHP',
            new Slug('php'),
        );
    }
}