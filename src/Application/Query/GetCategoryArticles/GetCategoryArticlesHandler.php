<?php

declare(strict_types=1);

namespace App\Application\Query\GetCategoryArticles;

use App\Domain\Contracts\Repository\ArticleRepository;
use App\Domain\Contracts\Repository\CategoryRepository;
use App\Domain\Entity\Article;
use App\Domain\ValueObjects\ArticleCursor;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetCategoryArticlesHandler
{
    public function __construct(
        private ArticleRepository $articlesRepository,
        private CategoryRepository $categoriesRepository,
    ) {
    }

    /**
     * @return array{items: list<Article>, nextCursor: ?string}
     */
    public function __invoke(GetCategoryArticlesQuery $query): array
    {
        $category = $this->categoriesRepository->findBySlug($query->categorySlug);

        if ($category === null) {
            throw new NotFoundHttpException('Категория не найдена.');
        }

        $cursorArray = $query->cursor !== null
            ? ['publishedAt' => $query->cursor->publishedAt, 'id' => $query->cursor->id]
            : null;

        $page = $this->articlesRepository->getFromCategoryWithCursor($category->id()->value, $query->limit, $cursorArray);

        $nextCursor = $page['hasMore'] && $page['items']
            ? (new ArticleCursor(
                $page['items'][count($page['items']) - 1]->publishedAt(),
                $page['items'][count($page['items']) - 1]->id()->value,
            ))->encode()
            : null;

        return ['items' => $page['items'], 'nextCursor' => $nextCursor];
    }
}