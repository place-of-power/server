<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Contracts\Repository\ArticleRepository as ArticleRepositoryInterface;
use App\Domain\Entity\Article;
use App\Domain\Types\ArticleStatus;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

/**
 * @extends ServiceEntityRepository<Article>
 */
#[AsAlias(ArticleRepositoryInterface::class)]
final class ArticleRepository extends ServiceEntityRepository implements ArticleRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * @return list<Article>
     */
    public function getRecent(int $limit): array
    {
        return $this->createQueryBuilder('a')
            ->where('a.status = :published')
            ->setParameter('published', ArticleStatus::Published)
            ->orderBy('a.publishedAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}