<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Domain\Entity\Article;
use App\Domain\Entity\Category;
use App\Domain\Types\ArticleStatus;
use App\Domain\ValueObjects\ArticleId;
use App\Domain\ValueObjects\Slug;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class ArticleFactory
{
    public static function create(
        EntityManagerInterface $entityManager,
        Category $category,
        string $slug,
        string $title,
        ArticleStatus $status = ArticleStatus::Published,
        DateTimeImmutable $now = new DateTimeImmutable(),
    ): Article {
        $article = Article::create(
            ArticleId::random(),
            new Slug($slug),
            $title,
            'Description',
            'Body',
            $status,
            $category,
            $now,
        );
        $entityManager->persist($article);
        $entityManager->flush();

        return $article;
    }
}