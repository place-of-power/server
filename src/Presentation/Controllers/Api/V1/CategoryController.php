<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\Api\V1;

use App\Application\Command\CreateCategory\CreateCategoryCommand;
use App\Domain\Entity\Category;
use App\Presentation\Http\ApiResponse;
use App\Presentation\Requests\Api\V1\CreateCategoryRequest;
use App\Presentation\Requests\Api\V1\ListCategoriesRequest;
use App\Presentation\Resources\Api\V1\CategoryResource;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    #[Route('/api/v1/categories', name: 'api_v1_categories', methods: ['GET'])]
    public function list(
        EntityManagerInterface $entityManager,

        #[MapQueryString(validationFailedStatusCode: Response::HTTP_UNPROCESSABLE_ENTITY)]
        ListCategoriesRequest $request,
    ): JsonResponse {
        $categories = $entityManager->getRepository(Category::class)
            ->findBy([], ['sortOrder' => 'ASC'], $request->limit);

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
}