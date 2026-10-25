<?php

declare(strict_types=1);

namespace App\Application\Command\CreateArticle;

use App\Domain\Contracts\Repository\CategoryRepository;
use App\Domain\Entity\Article;
use App\Domain\Types\ArticleStatus;
use App\Domain\ValueObjects\ArticleId;
use App\Domain\ValueObjects\Slug;
use DateTimeImmutable;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class CreateArticleHandler
{
    public function __construct(
        private CategoryRepository $categoriesRepository,
        private EntityManagerInterface $entityManager,
    ) {
    }

    public function __invoke(CreateArticleCommand $command): Article
    {
        $category = $this->categoriesRepository->findById($command->categoryId);

        if ($category === null) {
            throw new NotFoundHttpException('Категория не найдена.');
        }

        $article = Article::create(
            ArticleId::random(),
            new Slug($command->slug),
            $command->title,
            $command->description,
            $command->body,
            ArticleStatus::from($command->status),
            $category,
            new DateTimeImmutable(),
        );

        try {
            $this->entityManager->persist($article);
            $this->entityManager->flush();
        } catch (UniqueConstraintViolationException) {
            throw new ConflictHttpException('Статья с таким slug уже существует.');
        }

        return $article;
    }
}