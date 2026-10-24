<?php

declare(strict_types=1);

namespace App\Presentation\Requests\Api\V1;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ReorderCategoriesRequest
{
    /**
     * @param list<array{id: string, sortOrder: int}> $items
     */
    public function __construct(
        #[Assert\NotBlank(message: 'Список категорий не может быть пустым.')]
        #[Assert\All([
            new Assert\Collection(
                fields: [
                    'id' => new Assert\NotBlank(message: 'Идентификатор категории не может быть пустым.'),
                    'sortOrder' => new Assert\Type('integer', message: 'Порядок сортировки должен быть целым числом.'),
                ],
                allowExtraFields: false,
            ),
        ])]
        public array $items = [],
    ) {
    }
}