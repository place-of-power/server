<?php

declare(strict_types=1);

namespace App\Api\Controller;

use App\Application\Query\GetArticleQuery;
use App\Application\Query\ListArticlesQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

final class ArticleController extends AbstractController
{
    public function __construct(private readonly MessageBusInterface $messageBus)
    {
    }

    #[Route('/api/articles', name: 'api_articles', methods: ['GET'])]
    public function list(Request $request): Response
    {
        $query = new ListArticlesQuery(
            categorySlug: $this->stringParam($request, 'category'),
            status: null,
            q: $this->stringParam($request, 'q'),
            limit: $this->intParam($request, 'limit', 20),
            offset: $this->intParam($request, 'offset', 0),
        );

        $envelope = $this->messageBus->dispatch($query);

        $articles = $envelope->last(HandledStamp::class)?->getResult();

        return new JsonResponse($articles);
    }

    #[Route('/api/articles/{slug}', name: 'api_article', methods: ['GET'])]
    public function show(string $slug): Response
    {
        $envelope = $this->messageBus->dispatch(new GetArticleQuery($slug));

        $article = $envelope->last(HandledStamp::class)?->getResult();

        if ($article === null) {
            return new JsonResponse(['error' => 'Article not found'], Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($article);
    }

    private function stringParam(Request $request, string $name): ?string
    {
        $value = $request->query->get($name);

        return \is_string($value) && $value !== '' ? $value : null;
    }

    private function intParam(Request $request, string $name, int $default): int
    {
        $value = $request->query->get($name);

        return \is_numeric($value) ? (int) $value : $default;
    }
}