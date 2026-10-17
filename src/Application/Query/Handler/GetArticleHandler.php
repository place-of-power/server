<?php

declare(strict_types=1);

namespace App\Application\Query\Handler;

use App\Application\Query\Dto\ArticleDto;
use App\Application\Query\GetArticleQuery;
use App\Domain\Contracts\Repositories\ArticleRepository;
use App\Domain\ValueObjects\Slug;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetArticleHandler
{
    public function __construct(
        private ArticleRepository $articleRepository,
    ) {
    }

    public function __invoke(GetArticleQuery $query): ?ArticleDto
    {
        $article = $this->articleRepository->findBySlug(new Slug($query->slug));

        if ($article === null) {
            return null;
        }

        $category = $article->category();

        return new ArticleDto(
            id: $article->id()->value,
            slug: $article->slug()->value,
            title: $article->title(),
            description: $article->description(),
            body: $article->body(),
            categorySlug: $category->slug()->value,
            categoryName: $category->name(),
            publishedAt: $article->publishedAt()?->format('Y-m-d'),
            readingTime: null,
        );
    }
}