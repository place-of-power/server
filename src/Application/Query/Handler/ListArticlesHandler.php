<?php

declare(strict_types=1);

namespace App\Application\Query\Handler;

use App\Application\Query\Dto\ArticlePreviewDto;
use App\Application\Query\ListArticlesQuery;
use App\Domain\Contracts\Repositories\ArticleRepository;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ListArticlesHandler
{
    public function __construct(
        private ArticleRepository $articleRepository,
    ) {
    }

    /**
     * @return list<ArticlePreviewDto>
     */
    public function __invoke(ListArticlesQuery $query): array
    {
        $articles = $this->articleRepository->findPublished(
            $query->categorySlug,
            $query->q,
            $query->limit,
            $query->offset,
        );

        $result = [];

        foreach ($articles as $article) {
            $category = $article->category();
            $result[] = new ArticlePreviewDto(
                id: $article->id()->value,
                slug: $article->slug()->value,
                title: $article->title(),
                description: $article->description(),
                categorySlug: $category->slug()->value,
                categoryName: $category->name(),
                publishedAt: $article->publishedAt()?->format('Y-m-d'),
            );
        }

        return $result;
    }
}