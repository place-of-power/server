<?php

declare(strict_types=1);

namespace App\Tests\Factory;

use App\Domain\Entity\Category;
use App\Domain\ValueObjects\CategoryId;
use App\Domain\ValueObjects\Slug;
use Doctrine\ORM\EntityManagerInterface;

final class CategoryFactory
{
    public static function create(
        EntityManagerInterface $entityManager,
        string $name,
        string $slug,
        int $sortOrder = 0,
    ): Category {
        $category = Category::create(CategoryId::random(), $name, new Slug($slug), $sortOrder);
        $entityManager->persist($category);
        $entityManager->flush();

        return $category;
    }
}