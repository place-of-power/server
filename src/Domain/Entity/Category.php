<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObjects\CategoryId;
use App\Domain\ValueObjects\Slug;
use App\Infrastructure\Persistence\Repository\CategoryRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\Table(name: 'categories')]
#[ORM\UniqueConstraint(name: 'uq_categories_name', columns: ['name'])]
#[ORM\UniqueConstraint(name: 'uq_categories_slug', columns: ['slug'])]
class Category
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'guid')]
        private string $id,

        #[ORM\Column(type: 'string')]
        private string $name,

        #[ORM\Column(type: 'string')]
        private string $slug,

        #[ORM\Column(type: 'integer')]
        private int $sortOrder,

        #[ORM\Column(type: 'datetime_immutable')]
        private DateTimeImmutable $createdAt,

        #[ORM\Column(type: 'datetime_immutable')]
        private DateTimeImmutable $updatedAt,
    ) {
    }

    public static function create(
        CategoryId $id,
        string $name,
        Slug $slug,
        int $sortOrder = 0,
        DateTimeImmutable $now = new DateTimeImmutable(),
    ): self {
        return new self(
            id: $id->value,
            name: $name,
            slug: $slug->value,
            sortOrder: $sortOrder,
            createdAt: $now,
            updatedAt: $now,
        );
    }

    public function id(): CategoryId
    {
        return new CategoryId($this->id);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function slug(): Slug
    {
        return new Slug($this->slug);
    }

    public function sortOrder(): int
    {
        return $this->sortOrder;
    }

    public function changeSortOrder(int $sortOrder, DateTimeImmutable $now): void
    {
        $this->sortOrder = $sortOrder;
        $this->updatedAt = $now;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }
}
