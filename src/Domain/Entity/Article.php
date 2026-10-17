<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Types\ArticleStatus;
use App\Domain\ValueObjects\ArticleId;
use App\Domain\ValueObjects\CategoryId;
use App\Domain\ValueObjects\Slug;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'articles')]
#[ORM\UniqueConstraint(name: 'uq_articles_slug', columns: ['slug'])]
#[ORM\Index(name: 'idx_articles_category', columns: ['category_id'])]
class Article
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'guid')]
        private string $id,

        #[ORM\Column(type: 'string')]
        private string $slug,

        #[ORM\Column(type: 'string')]
        private string $title,

        #[ORM\Column(type: 'text')]
        private string $body,

        #[ORM\Column(type: 'text')]
        private string $description,

        #[ORM\Column(type: 'string', enumType: ArticleStatus::class)]
        private ArticleStatus $status,

        #[ORM\Column(type: 'datetime_immutable')]
        private DateTimeImmutable $createdAt,

        #[ORM\Column(type: 'datetime_immutable')]
        private DateTimeImmutable $updatedAt,

        #[ORM\ManyToOne(targetEntity: Category::class)]
        #[ORM\JoinColumn(name: 'category_id', referencedColumnName: 'id', nullable: false)]
        private Category $category,

        #[ORM\Column(type: 'datetime_immutable', nullable: true)]
        private ?DateTimeImmutable $publishedAt = null,
    ) {
    }

    public static function create(
        ArticleId $id,
        Slug $slug,
        string $title,
        string $description,
        string $body,
        ArticleStatus $status,
        Category $category,
        DateTimeImmutable $now,
    ): self {
        return new self(
            id: $id->value,
            slug: $slug->value,
            title: $title,
            body: $body,
            description: $description,
            status: $status,
            createdAt: $now,
            updatedAt: $now,
            category: $category,
            publishedAt: $status === ArticleStatus::Published ? $now : null,
        );
    }

    public function publish(DateTimeImmutable $now = new DateTimeImmutable()): void
    {
        $this->status = ArticleStatus::Published;
        $this->publishedAt = $now;
        $this->updatedAt = $now;
    }

    public function id(): ArticleId
    {
        return new ArticleId($this->id);
    }

    public function slug(): Slug
    {
        return new Slug($this->slug);
    }

    public function title(): string
    {
        return $this->title;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function body(): string
    {
        return $this->body;
    }

    public function status(): ArticleStatus
    {
        return $this->status;
    }

    public function category(): Category
    {
        return $this->category;
    }

    public function categoryId(): CategoryId
    {
        return $this->category->id();
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function publishedAt(): ?DateTimeImmutable
    {
        return $this->publishedAt;
    }
}