<?php

declare(strict_types=1);

namespace App\Application\Command\CreateCategory;

use App\Domain\Entity\Category;
use App\Domain\ValueObjects\CategoryId;
use App\Domain\ValueObjects\Slug;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateCategoryHandler
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(CreateCategoryCommand $command): Category
    {
        $category = Category::create(
            CategoryId::random(),
            $command->name,
            new Slug($command->slug),
            $command->sortOrder,
        );

        try {
            $this->entityManager->persist($category);
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException) {
            throw new ConflictHttpException('Категория с таким названием или slug уже существует.');
        }

        return $category;
    }
}