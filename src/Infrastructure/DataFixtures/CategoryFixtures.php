<?php

declare(strict_types=1);

namespace App\Infrastructure\DataFixtures;

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
            ['name' => 'Сети и протоколы', 'slug' => 'networking', 'sortOrder' => 1],
            ['name' => 'Надёжность и асинхронность', 'slug' => 'reliability', 'sortOrder' => 2],
            ['name' => 'Архитектура', 'slug' => 'architecture', 'sortOrder' => 3],
            ['name' => 'СУБД', 'slug' => 'databases', 'sortOrder' => 4],
            ['name' => 'Инженерия и безопасность', 'slug' => 'engineering-security', 'sortOrder' => 5],
            ['name' => 'PHP', 'slug' => 'php', 'sortOrder' => 6],
            ['name' => 'Паттерны', 'slug' => 'patterns', 'sortOrder' => 7],
            ['name' => 'Laravel', 'slug' => 'laravel', 'sortOrder' => 8],
            ['name' => 'Symfony', 'slug' => 'symfony', 'sortOrder' => 9],
            ['name' => 'TypeScript', 'slug' => 'typescript', 'sortOrder' => 10],
            ['name' => 'React', 'slug' => 'react', 'sortOrder' => 11],
            ['name' => 'Vue', 'slug' => 'vue', 'sortOrder' => 12],
        ];

        foreach ($categories as $data) {
            $manager->persist(Category::create(
                CategoryId::random(),
                $data['name'],
                new Slug($data['slug']),
                $data['sortOrder'],
            ));
        }

        $manager->flush();
    }
}
