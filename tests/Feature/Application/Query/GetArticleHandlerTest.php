<?php

declare(strict_types=1);

namespace App\Tests\Feature\Application\Query;

use App\Application\Query\GetArticle\GetArticleQuery;
use App\Domain\Entity\Article;
use App\Domain\Types\ArticleStatus;
use App\Tests\Factory\ArticleFactory;
use App\Tests\Factory\CategoryFactory;
use App\Tests\Feature\HandlerTestCase;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class GetArticleHandlerTest extends HandlerTestCase
{
    public function testReturnsArticleBySlug(): void
    {
        $category = CategoryFactory::create($this->entityManager, 'PHP', 'php', 0);
        ArticleFactory::create($this->entityManager, $category, 'php-article', 'PHP Article', ArticleStatus::Published);

        $envelope = $this->messageBus->dispatch(new GetArticleQuery('php-article'));
        /** @var Article $article */
        $article = $envelope->last(HandledStamp::class)->getResult();

        self::assertSame('php-article', $article->slug()->value);
        self::assertSame('PHP Article', $article->title());
        self::assertSame($category->id()->value, $article->categoryId()->value);
    }

    public function testMissingArticleThrowsNotFound(): void
    {
        try {
            $this->messageBus->dispatch(new GetArticleQuery('nope'));
            self::fail('Dispatcher should have thrown.');
        } catch (HandlerFailedException $e) {
            self::assertInstanceOf(NotFoundHttpException::class, $e->getPrevious());
        }
    }
}