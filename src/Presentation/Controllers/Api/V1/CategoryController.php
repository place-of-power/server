<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Api\V1;

use App\Application\Command\CreateCategory\CreateCategoryCommand;
use App\Application\Command\DeleteCategory\DeleteCategoryCommand;
use App\Application\Command\ReorderCategories\ReorderCategoriesCommand;
use App\Application\Query\GetCategoryArticles\GetCategoryArticlesQuery;
use App\Application\Query\ListCategories\ListCategoriesQuery;
use App\Domain\Entity\Category;
use App\Domain\ValueObjects\ArticleCursor;
use App\Presentation\Http\ApiResponse;
use App\Presentation\Requests\Api\V1\CreateCategoryRequest;
use App\Presentation\Requests\Api\V1\GetCategoryArticlesRequest;
use App\Presentation\Requests\Api\V1\ListCategoriesRequest;
use App\Presentation\Requests\Api\V1\ReorderCategoriesRequest;
use App\Presentation\Resources\Api\V1\ArticlePreviewResource;
use App\Presentation\Resources\Api\V1\CategoryResource;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/api/v1/categories', name: 'api_v1_categories', methods: ['GET'])]
    public function list(
        #[MapQueryString(validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY)]
        ListCategoriesRequest $request,

        MessageBusInterface $messageBus,
    ): JsonResponse {
        $envelope = $messageBus->dispatch(new ListCategoriesQuery($request->limit));
        /** @var list<Category> $categories */
        $categories = $envelope->last(HandledStamp::class)->getResult();

        return ApiResponse::success(array_map(CategoryResource::fromEntity(...), $categories));
    }

    #[Route('/api/v1/categories', name: 'api_v1_categories_create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload(validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY)]
        CreateCategoryRequest $request,

        MessageBusInterface $messageBus,
    ): JsonResponse {
        $envelope = $messageBus->dispatch(new CreateCategoryCommand(
            $request->name,
            $request->slug,
            $request->sortOrder,
        ));

        $category = $envelope->last(HandledStamp::class)?->getResult();

        return ApiResponse::success(CategoryResource::fromEntity($category), Response::HTTP_CREATED);
    }

    #[Route('/api/v1/categories/{id}', name: 'api_v1_categories_delete', methods: ['DELETE'])]
    public function delete(string $id, MessageBusInterface $messageBus): JsonResponse
    {
        $messageBus->dispatch(new DeleteCategoryCommand($id));

        return ApiResponse::success(null);
    }

    #[Route('/api/v1/categories/reorder', name: 'api_v1_categories_reorder', methods: ['PUT'])]
    public function reorder(
        #[MapRequestPayload(validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY)]
        ReorderCategoriesRequest $request,

        MessageBusInterface $messageBus,
    ): JsonResponse {
        $messageBus->dispatch(new ReorderCategoriesCommand($request->items));

        return ApiResponse::success(null);
    }

    #[Route('/api/v1/categories/{slug}/articles', name: 'api_v1_categories_articles', methods: ['GET'])]
    public function articles(
        string $slug,
        #[MapQueryString(validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY)]
        GetCategoryArticlesRequest $request,

        MessageBusInterface $messageBus,
    ): JsonResponse {
        try {
            $cursor = ArticleCursor::fromString($request->cursor);
        } catch (\InvalidArgumentException) {
            throw new BadRequestHttpException('Неверный курсор пагинации.');
        }

        $envelope = $messageBus->dispatch(new GetCategoryArticlesQuery(
            $slug,
            $request->limit,
            $cursor,
        ));
        /** @var array{items: list<Category>, nextCursor: ?string} $result */
        $result = $envelope->last(HandledStamp::class)->getResult();

        return ApiResponse::success([
            'items' => array_map(ArticlePreviewResource::fromEntity(...), $result['items']),
            'nextCursor' => $result['nextCursor'],
        ]);
    }
}