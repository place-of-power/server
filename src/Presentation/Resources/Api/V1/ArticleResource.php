<?php

declare(strict_types=1);

namespace App\Presentation\Resources\Api\V1;

use App\Domain\Entity\Article;

final readonly class ArticleResource
{
    public function __construct(
        public string $id,
        public string $slug,
        public string $title,
        public string $description,
        public string $body,
        public string $status,
        public string $categoryId,
        public string $categorySlug,
    ) {
    }

    public static function fromEntity(Article $article): self
    {
        return new self(
            $article->id()->value,
            $article->slug()->value,
            $article->title(),
            $article->description(),
            $article->body(),
            $article->status()->value,
            $article->categoryId()->value,
            $article->category()->slug()->value,
        );
    }
}