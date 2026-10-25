<?php

declare(strict_types=1);

namespace App\Tests\Feature\Application\Query;

use App\Application\Query\GetCategoryArticles\GetCategoryArticlesQuery;
use App\Domain\Entity\Article;
use App\Domain\Types\ArticleStatus;
use App\Domain\ValueObjects\ArticleCursor;
use App\Tests\Factory\ArticleFactory;
use App\Tests\Factory\CategoryFactory;
use App\Tests\Feature\HandlerTestCase;
use DateTimeImmutable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class GetCategoryArticlesHandlerTest extends HandlerTestCase
{
    public function testReturnsOnlyPublishedOfCategoryWithNextCursor(): void
    {
        $php = CategoryFactory::create($this->entityManager, 'PHP', 'php', 0);
        $laravel = CategoryFactory::create($this->entityManager, 'Laravel', 'laravel', 1);

        ArticleFactory::create($this->entityManager, $php, 'a1', 'A1', ArticleStatus::Published, new DateTimeImmutable('2026-01-01'));
        ArticleFactory::create($this->entityManager, $php, 'a2', 'A2', ArticleStatus::Published, new DateTimeImmutable('2026-02-01'));
        ArticleFactory::create($this->entityManager, $laravel, 'other', 'Other', ArticleStatus::Published, new DateTimeImmutable('2026-03-01'));

        $envelope = $this->messageBus->dispatch(new GetCategoryArticlesQuery('php', 1, null));
        /** @var array{items: list<Article>, nextCursor: ?string} $result */
        $result = $envelope->last(HandledStamp::class)->getResult();

        self::assertCount(1, $result['items']);
        self::assertSame('a2', $result['items'][0]->slug()->value);
        self::assertNotNull($result['nextCursor']);

        // вторая страница по курсору
        $page2 = $this->messageBus
            ->dispatch(new GetCategoryArticlesQuery('php', 1, ArticleCursor::fromString($result['nextCursor'])))
            ->last(HandledStamp::class)
            ->getResult();

        self::assertSame('a1', $page2['items'][0]->slug()->value);
        self::assertNull($page2['nextCursor']);
    }

    public function testMissingCategoryThrowsNotFound(): void
    {
        try {
            $this->messageBus->dispatch(new GetCategoryArticlesQuery('nope', 10, null));
            self::fail('Dispatcher should have thrown.');
        } catch (HandlerFailedException $e) {
            self::assertInstanceOf(NotFoundHttpException::class, $e->getPrevious());
        }
    }
}