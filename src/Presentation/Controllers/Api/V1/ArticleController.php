<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Api\V1;

use App\Application\Command\CreateArticle\CreateArticleCommand;
use App\Application\Query\GetArticle\GetArticleQuery;
use App\Application\Query\GetRecentArticles\GetRecentArticlesQuery;
use App\Domain\Entity\Article;
use App\Presentation\Http\ApiResponse;
use App\Presentation\Requests\Api\V1\CreateArticleRequest;
use App\Presentation\Requests\Api\V1\GetRecentArticlesRequest;
use App\Presentation\Resources\Api\V1\ArticlePreviewResource;
use App\Presentation\Resources\Api\V1\ArticleResource;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

final class ArticleController extends AbstractController
{
    #[Route('/api/v1/articles/recent', name: 'api_v1_articles_recent', methods: ['GET'])]
    public function recent(
        #[MapQueryString(validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY)]
        GetRecentArticlesRequest $request,

        MessageBusInterface $messageBus,
    ): JsonResponse {
        $envelope = $messageBus->dispatch(new GetRecentArticlesQuery($request->limit));
        /** @var list<Article> $articles */
        $articles = $envelope->last(HandledStamp::class)->getResult();

        return ApiResponse::success(array_map(ArticlePreviewResource::fromEntity(...), $articles));
    }

    #[Route('/api/v1/articles/{slug}', name: 'api_v1_articles_show', methods: ['GET'])]
    public function show(string $slug, MessageBusInterface $messageBus): JsonResponse
    {
        $envelope = $messageBus->dispatch(new GetArticleQuery($slug));
        $article = $envelope->last(HandledStamp::class)->getResult();

        return ApiResponse::success(ArticleResource::fromEntity($article));
    }

    #[Route('/api/v1/articles', name: 'api_v1_articles_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY)]
        CreateArticleRequest $request,

        MessageBusInterface $messageBus,
    ): JsonResponse {
        $envelope = $messageBus->dispatch(new CreateArticleCommand(
            $request->slug,
            $request->title,
            $request->description,
            $request->body,
            $request->status,
            $request->categoryId,
        ));

        $article = $envelope->last(HandledStamp::class)?->getResult();

        return ApiResponse::success(ArticleResource::fromEntity($article), Response::HTTP_CREATED);
    }
}