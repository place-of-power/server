<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Domain\Contracts\Repositories\CategoryRepository as CategoryRepositoryInterface;
use App\Domain\Entity\Category;
use App\Domain\ValueObjects\CategoryId;
use App\Domain\ValueObjects\Slug;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\AsAlias;

#[AsAlias(CategoryRepositoryInterface::class)]
final readonly class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function save(Category $category): void
    {
        $this->em->persist($category);
    }

    public function findById(CategoryId $id): ?Category
    {
        return $this->em->getRepository(Category::class)->find($id->value);
    }

    public function findBySlug(Slug $slug): ?Category
    {
        return $this->em->getRepository(Category::class)->findOneBy(['slug' => $slug->value]);
    }

    public function findAll(): array
    {
        return $this->em->getRepository(Category::class)->findAll();
    }
}