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

    /**
     * @param array{publishedAt: \DateTimeImmutable, id: string} $cursor
     *
     * @return array{items: list<Article>, hasMore: bool}
     */
    public function getFromCategoryWithCursor(string $categoryId, int $limit, ?array $cursor): array
    {
        $qb = $this->createQueryBuilder('a')
            ->where('a.status = :published')
            ->andWhere('a.category = :category')
            ->setParameter('published', ArticleStatus::Published)
            ->setParameter('category', $categoryId)
            ->orderBy('a.publishedAt', 'DESC')
            ->addOrderBy('a.id', 'DESC')
            ->setMaxResults($limit + 1);

        if ($cursor !== null) {
            $qb->andWhere('a.publishedAt < :cursorAt OR (a.publishedAt = :cursorAt AND a.id < :cursorId)')
                ->setParameter('cursorAt', $cursor['publishedAt'])
                ->setParameter('cursorId', $cursor['id']);
        }

        $rows = $qb->getQuery()->getResult();

        $hasMore = count($rows) > $limit;
        $items = $hasMore ? array_slice($rows, 0, $limit) : $rows;

        return ['items' => $items, 'hasMore' => $hasMore];
    }

    public function findBySlug(string $slug): ?Article
    {
        return $this->findOneBy(['slug' => $slug]);
    }
}