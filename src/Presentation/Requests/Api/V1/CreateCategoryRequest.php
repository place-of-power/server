<?php

declare(strict_types=1);

namespace App\Presentation\Requests\Api\V1;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateCategoryRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Укажите название категории.')]
        #[Assert\Length(max: 255, maxMessage: 'Название не должно превышать 255 символов.')]
        public string $name = '',

        #[Assert\NotBlank(message: 'Укажите slug категории.')]
        #[Assert\Length(max: 255, maxMessage: 'Slug не должен превышать 255 символов.')]
        #[Assert\Regex(
            pattern: '/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            message: 'Slug может содержать только латиницу, цифры и дефисы.',
        )]
        public string $slug = '',

        #[Assert\PositiveOrZero(message: 'Порядок сортировки должен быть неотрицательным.')]
        public int $sortOrder = 0,
    ) {
    }
}