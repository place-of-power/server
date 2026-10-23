<?php

declare(strict_types=1);

namespace App\Tests\Feature\Application\Command;

use App\Application\Command\CreateCategory\CreateCategoryCommand;
use App\Domain\Entity\Category;
use App\Tests\Feature\HandlerTestCase;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Messenger\Exception\HandlerFailedException;
use Symfony\Component\Messenger\Stamp\HandledStamp;

final class CreateCategoryHandlerTest extends HandlerTestCase
{
    public function testCreatesCategory(): void
    {
        $envelope = $this->messageBus->dispatch(new CreateCategoryCommand('Архитектура', 'architecture', 10));

        $category = $envelope->last(HandledStamp::class)->getResult();

        self::assertInstanceOf(Category::class, $category);
        self::assertSame('Архитектура', $category->name());
        self::assertSame('architecture', $category->slug()->value);
        self::assertSame(10, $category->sortOrder());
        self::assertCount(1, $this->entityManager->getRepository(Category::class)->findAll());
    }

    public function testDuplicateNameRejected(): void
    {
        $this->messageBus->dispatch(new CreateCategoryCommand('PHP', 'php', 0));

        $this->expectConflict('PHP', 'another-slug', 1);
    }

    public function testDuplicateSlugRejected(): void
    {
        $this->messageBus->dispatch(new CreateCategoryCommand('PHP', 'php', 0));

        $this->expectConflict('Laravel', 'php', 1);
    }

    private function expectConflict(string $name, string $slug, int $sortOrder): void
    {
        try {
            $this->messageBus->dispatch(new CreateCategoryCommand($name, $slug, $sortOrder));
            self::fail('Dispatcher should have thrown.');
        } catch (HandlerFailedException $e) {
            self::assertInstanceOf(ConflictHttpException::class, $e->getPrevious());
        }
    }
}