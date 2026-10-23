<?php

declare(strict_types=1);

namespace App\Tests\Feature\Application\Command;

use App\Application\Command\CreateCategory\CreateCategoryCommand;
use App\Application\Command\DeleteCategory\DeleteCategoryCommand;
use App\Domain\Entity\Article;
use App\Domain\Entity\Category;
use App\Domain\Types\ArticleStatus;
use App\Domain\ValueObjects\ArticleId;
use App\Domain\ValueObjects\Slug;
use App\Tests\Feature\HandlerTestCase;
use DateTimeImmutable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class DeleteCategoryHandlerTest extends HandlerTestCase
{
    public function testDeletesCategoryAndItsArticles(): void
    {
        $envelope = $this->messageBus->dispatch(new CreateCategoryCommand('PHP', 'php', 0));
        /** @var Category $category */
        $category = $envelope->last(HandledStamp::class)->getResult();
        $this->entityManager->persist($this->article($category));
        $this->entityManager->flush();

        ($this->messageBus->dispatch(new DeleteCategoryCommand($category->id()->value)));

        self::assertCount(0, $this->entityManager->getRepository(Category::class)->findAll());
        self::assertCount(0, $this->entityManager->getRepository(Article::class)->findAll());
    }

    public function testDeleteMissingCategoryThrowsNotFound(): void
    {
        try {
            $this->messageBus->dispatch(new DeleteCategoryCommand('00000000-0000-0000-0000-000000000000'));
            self::fail('Dispatcher should have thrown.');
        } catch (HandlerFailedException $e) {
            self::assertInstanceOf(NotFoundHttpException::class, $e->getPrevious());
        }
    }

    public function testDeleteInvalidIdThrowsNotFound(): void
    {
        try {
            $this->messageBus->dispatch(new DeleteCategoryCommand('not-a-uuid'));
            self::fail('Dispatcher should have thrown.');
        } catch (HandlerFailedException $e) {
            self::assertInstanceOf(NotFoundHttpException::class, $e->getPrevious());
        }
    }

    private function article(Category $category): Article
    {
        return Article::create(
            ArticleId::random(),
            new Slug('php-article'),
            'PHP Article',
            'Description',
            'Body',
            ArticleStatus::Published,
            $category,
            new DateTimeImmutable(),
        );
    }
}