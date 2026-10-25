<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Api\V1;

use App\Application\Command\CreateArticle\CreateArticleCommand;
use App\Domain\Entity\Article;
use App\Presentation\Http\ApiResponse;
use App\Presentation\Requests\Api\V1\CreateArticleRequest;
use App\Presentation\Resources\Api\V1\ArticleResource;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

final class ArticleController extends AbstractController
{
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