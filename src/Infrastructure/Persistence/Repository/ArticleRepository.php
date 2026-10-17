<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Contracts\Repositories\ArticleRepository as ArticleRepositoryInterface;
use App\Domain\Entity\Article;
use App\Domain\Types\ArticleStatus;
use App\Domain\ValueObjects\ArticleId;
use App\Domain\ValueObjects\Slug;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias(ArticleRepositoryInterface::class)]
final readonly class ArticleRepository implements ArticleRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function save(Article $article): void
    {
        $this->em->persist($article);
    }

    public function remove(Article $article): void
    {
        $this->em->remove($article);
    }

    public function findById(ArticleId $id): ?Article
    {
        return $this->em->getRepository(Article::class)->find($id->value);
    }

    public function findBySlug(Slug $slug): ?Article
    {
        return $this->em->getRepository(Article::class)->findOneBy(['slug' => $slug->value]);
    }

    public function findPublished(?string $categorySlug, ?string $q, int $limit, int $offset): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('a')
            ->from(Article::class, 'a')
            ->join('a.category', 'c')
            ->where('a.status = :status')
            ->setParameter('status', ArticleStatus::Published)
            ->orderBy('a.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $this->applyFilters($qb, $categorySlug, $q);

        return $qb->getQuery()->getResult();
    }

    private function applyFilters(QueryBuilder $qb, ?string $categorySlug, ?string $q): void
    {
        if ($categorySlug !== null) {
            $qb->andWhere('c.slug = :categorySlug')
                ->setParameter('categorySlug', $categorySlug);
        }

        if ($q !== null && $q !== '') {
            $qb->andWhere('(a.title LIKE :q OR a.description LIKE :q OR a.body LIKE :q)')
                ->setParameter('q', '%' . $q . '%');
        }
    }
}