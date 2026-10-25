<?php

declare(strict_types=1);

namespace App\Presentation\Resources\Api\V1;

use App\Domain\Entity\Article;

final readonly class ArticlePreviewResource
{
    public function __construct(
        public string $slug,
        public string $title,
        public string $description,
        public string $status,
        public string $categorySlug,
        public string $categoryName,
        public ?string $publishedAt,
    ) {
    }

    public static function fromEntity(Article $article): self
    {
        return new self(
            $article->slug()->value,
            $article->title(),
            $article->description(),
            $article->status()->value,
            $article->category()->slug()->value,
            $article->category()->name(),
            $article->publishedAt()?->format('Y-m-d'),
        );
    }
}