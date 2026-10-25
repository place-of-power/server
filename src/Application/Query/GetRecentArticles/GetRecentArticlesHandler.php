<?php

declare(strict_types=1);

namespace App\Application\Query\GetRecentArticles;

use App\Domain\Contracts\Repository\ArticleRepository;
use App\Domain\Entity\Article;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetRecentArticlesHandler
{
    public function __construct(private ArticleRepository $articlesRepository)
    {
    }

    /**
     * @return list<Article>
     */
    public function __invoke(GetRecentArticlesQuery $query): array
    {
        return $this->articlesRepository->getRecent($query->limit);
    }
}