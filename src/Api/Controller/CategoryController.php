<?php

declare(strict_types=1);

namespace App\Api\Controller;

use App\Application\Query\ListCategoriesQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

final class CategoryController extends AbstractController
{
    public function __construct(private readonly MessageBusInterface $messageBus)
    {
    }

    #[Route('/api/categories', name: 'api_categories', methods: ['GET'])]
    public function list(): Response
    {
        $envelope = $this->messageBus->dispatch(new ListCategoriesQuery());
        $categories = $envelope->last(HandledStamp::class)?->getResult();

        return new JsonResponse($categories);
    }
}