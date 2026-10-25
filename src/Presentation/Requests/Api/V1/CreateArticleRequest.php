<?php

declare(strict_types=1);

namespace App\Presentation\Requests\Api\V1;

use App\Domain\Types\ArticleStatus;
use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateArticleRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Укажите slug статьи.')]
        #[Assert\Regex(
            pattern: '/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
            message: 'Slug может содержать только латиницу, цифры и дефисы.',
        )]
        public ?string $slug = null,

        #[Assert\NotBlank(message: 'Укажите заголовок статьи.')]
        #[Assert\Length(max: 255, maxMessage: 'Заголовок не должен превышать 255 символов.')]
        public ?string $title = null,

        #[Assert\NotBlank(message: 'Укажите описание статьи.')]
        public ?string $description = null,

        #[Assert\NotBlank(message: 'Укажите содержимое статьи.')]
        public ?string $body = null,

        #[Assert\NotBlank(message: 'Укажите статус статьи.')]
        #[Assert\Choice(choices: [ArticleStatus::Draft->value, ArticleStatus::Published->value, ArticleStatus::Archived->value])]
        public ?string $status = null,

        #[Assert\NotBlank(message: 'Укажите категорию статьи.')]
        public ?string $categoryId = null,
    ) {
    }
}