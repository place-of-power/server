<?php

declare(strict_types=1);

namespace App\Presentation\Http;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final class ApiResponse
{
    public static function success(mixed $data, int $status = Response::HTTP_OK): JsonResponse
    {
        return new JsonResponse(['data' => $data, 'errors' => []], $status);
    }

    /**
     * @param list<array{field: ?string, message: string}> $errors
     */
    public static function error(int $status, array $errors): JsonResponse
    {
        return new JsonResponse(['data' => null, 'errors' => $errors], $status);
    }
}
