<?php

declare(strict_types=1);

namespace App\Application\Query\GetArticle;

use App\Domain\Contracts\Repository\ArticleRepository;
use App\Domain\Entity\Article;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class GetArticleHandler
{
    public function __construct(private ArticleRepository $articlesRepository)
    {
    }

    public function __invoke(GetArticleQuery $query): Article
    {
        $article = $this->articlesRepository->findBySlug($query->slug);

        if ($article === null) {
            throw new NotFoundHttpException('Статья не найдена.');
        }

        return $article;
    }
}