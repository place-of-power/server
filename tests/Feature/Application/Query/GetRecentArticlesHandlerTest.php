<?php

declare(strict_types=1);

namespace App\Tests\Feature\Application\Query;

use App\Application\Query\GetRecentArticles\GetRecentArticlesQuery;
use App\Domain\Entity\Article;
use App\Domain\Types\ArticleStatus;
use App\Tests\Factory\ArticleFactory;
use App\Tests\Factory\CategoryFactory;
use App\Tests\Feature\HandlerTestCase;
use DateTimeImmutable;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class GetRecentArticlesHandlerTest extends HandlerTestCase
{
    public function testReturnsOnlyPublishedOrderedByPublishedAtDesc(): void
    {
        $category = CategoryFactory::create($this->entityManager, 'PHP', 'php', 0);

        ArticleFactory::create($this->entityManager, $category, 'old', 'Олд', ArticleStatus::Published, new DateTimeImmutable('2026-01-01'));
        ArticleFactory::create($this->entityManager, $category, 'new', 'Новый', ArticleStatus::Published, new DateTimeImmutable('2026-10-01'));
        ArticleFactory::create($this->entityManager, $category, 'draft', 'Черновик', ArticleStatus::Draft, new DateTimeImmutable('2026-12-01'));

        $envelope = $this->messageBus->dispatch(new GetRecentArticlesQuery(10));
        /** @var list<Article> $articles */
        $articles = $envelope->last(HandledStamp::class)->getResult();

        self::assertSame(['new', 'old'], array_map(
            static fn (Article $a): string => $a->slug()->value,
            $articles,
        ));
    }

    public function testLimitRestrictsCount(): void
    {
        $category = CategoryFactory::create($this->entityManager, 'PHP', 'php', 0);

        ArticleFactory::create($this->entityManager, $category, 'a1', 'A1', ArticleStatus::Published, new DateTimeImmutable('2026-01-01'));
        ArticleFactory::create($this->entityManager, $category, 'b2', 'B2', ArticleStatus::Published, new DateTimeImmutable('2026-02-01'));
        ArticleFactory::create($this->entityManager, $category, 'c3', 'C3', ArticleStatus::Published, new DateTimeImmutable('2026-03-01'));

        $envelope = $this->messageBus->dispatch(new GetRecentArticlesQuery(2));
        /** @var list<Article> $articles */
        $articles = $envelope->last(HandledStamp::class)->getResult();

        self::assertSame(['c3', 'b2'], array_map(
            static fn (Article $a): string => $a->slug()->value,
            $articles,
        ));
    }
}