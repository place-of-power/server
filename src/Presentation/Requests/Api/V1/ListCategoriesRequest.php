<?php

declare(strict_types=1);

namespace App\Presentation\Requests\Api\V1;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ListCategoriesRequest
{
    public function __construct(
        #[Assert\Positive(message: 'Параметр limit должен быть целым числом больше нуля.')]
        public ?int $limit = null,
    ) {
    }
}
