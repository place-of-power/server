<?php

declare(strict_types=1);

namespace App\Presentation\Requests\Api\V1;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class GetCategoryArticlesRequest
{
    public function __construct(
        #[Assert\Type('integer', message: 'Параметр limit должен быть целым числом.')]
        #[Assert\Positive(message: 'Параметр limit должен быть больше нуля.')]
        public int $limit = 10,

        public ?string $cursor = null,
    ) {
    }
}