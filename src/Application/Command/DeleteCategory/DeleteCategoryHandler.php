<?php

declare(strict_types=1);

namespace App\Application\Command\DeleteCategory;

use App\Domain\Entity\Article;
use App\Domain\Entity\Category;
use App\Domain\ValueObjects\CategoryId;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class DeleteCategoryHandler
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function __invoke(DeleteCategoryCommand $command): void
    {
        try {
            $id = new CategoryId($command->id);
        } catch (\InvalidArgumentException) {
            throw new NotFoundHttpException('Категория не найдена.');
        }

        $category = $this->entityManager->getRepository(Category::class)->find($id->value);

        if ($category === null) {
            throw new NotFoundHttpException('Категория не найдена.');
        }

        $this->entityManager->createQueryBuilder()
            ->delete(Article::class, 'a')
            ->where('a.category = :category')
            ->setParameter('category', $category)
            ->getQuery()
            ->execute();

        $this->entityManager->remove($category);
        $this->entityManager->flush();
    }
}