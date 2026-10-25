<?php

declare(strict_types=1);

namespace App\Tests\Feature\Application\Command;

use App\Application\Command\CreateArticle\CreateArticleCommand;
use App\Domain\Entity\Article;
use App\Domain\Entity\Category;
use App\Tests\Factory\CategoryFactory;
use App\Tests\Feature\HandlerTestCase;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class CreateArticleHandlerTest extends HandlerTestCase
{
    public function testCreatesArticle(): void
    {
        $category = CategoryFactory::create($this->entityManager, 'PHP', 'php', 0);

        $envelope = $this->messageBus->dispatch(new CreateArticleCommand(
            'php-article',
            'PHP Article',
            'Description',
            'Body',
            'draft',
            $category->id()->value,
        ));

        /** @var Article $article */
        $article = $envelope->last(HandledStamp::class)->getResult();

        self::assertSame('php-article', $article->slug()->value);
        self::assertSame('PHP Article', $article->title());
        self::assertSame($category->id()->value, $article->categoryId()->value);
        self::assertCount(1, $this->entityManager->getRepository(Article::class)->findAll());
    }

    public function testMissingCategoryThrowsNotFound(): void
    {
        try {
            $this->messageBus->dispatch(new CreateArticleCommand(
                'php-article',
                'PHP Article',
                'Desc',
                'Body',
                'draft',
                '00000000-0000-0000-0000-000000000000',
            ));
            self::fail('Dispatcher should have thrown.');
        } catch (HandlerFailedException $e) {
            self::assertInstanceOf(NotFoundHttpException::class, $e->getPrevious());
        }
    }

    public function testDuplicateSlugThrowsConflict(): void
    {
        $category = CategoryFactory::create($this->entityManager, 'PHP', 'php', 0);

        $this->messageBus->dispatch(new CreateArticleCommand(
            'php-article',
            'First',
            'Desc',
            'Body',
            'draft',
            $category->id()->value,
        ));

        try {
            $this->messageBus->dispatch(new CreateArticleCommand(
                'php-article',
                'Second',
                'Desc',
                'Body',
                'draft',
                $category->id()->value,
            ));
            self::fail('Dispatcher should have thrown.');
        } catch (HandlerFailedException $e) {
            self::assertInstanceOf(ConflictHttpException::class, $e->getPrevious());
        }
    }
}