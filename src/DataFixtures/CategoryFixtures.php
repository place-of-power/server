<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Domain\Entity\Category;
use App\Domain\ValueObjects\CategoryId;
use App\Domain\ValueObjects\Slug;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $categories = [
            ['name' => 'PHP', 'slug' => 'php', 'sortOrder' => 1],
            ['name' => 'JavaScript', 'slug' => 'javascript', 'sortOrder' => 2],
            ['name' => 'Docker', 'slug' => 'docker', 'sortOrder' => 3],
            ['name' => 'Symfony', 'slug' => 'symfony', 'sortOrder' => 4],
            ['name' => 'PostgreSQL', 'slug' => 'postgresql', 'sortOrder' => 5],
            ['name' => 'Vue', 'slug' => 'vue', 'sortOrder' => 6],
        ];

        $repo = $manager->getRepository(Category::class);

        foreach ($categories as $data) {
            $exists = $repo->findOneBy(['slug' => $data['slug']])
                ?? $repo->findOneBy(['name' => $data['name']]);

            if ($exists !== null) {
                continue;
            }

            $category = Category::create(
                CategoryId::random(),
                $data['name'],
                new Slug($data['slug']),
                $data['sortOrder'],
            );

            $manager->persist($category);
        }

        $manager->flush();
    }
}
