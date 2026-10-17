<?php

declare(strict_types=1);

namespace App\Presentation\Http;

use Symfony\Component\HttpFoundation\JsonResponse;

final class ApiResponse
{
    public static function success(mixed $data): JsonResponse
    {
        return new JsonResponse(['data' => $data, 'errors' => []]);
    }

    /**
     * @param list<array{field: ?string, message: string}> $errors
     */
    public static function error(int $status, array $errors): JsonResponse
    {
        return new JsonResponse(['data' => null, 'errors' => $errors], $status);
    }
}
