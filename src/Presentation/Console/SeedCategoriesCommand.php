<?php

declare(strict_types=1);

namespace App\Presentation\Console;

use App\Domain\Entity\Category;
use App\Domain\ValueObjects\CategoryId;
use App\Domain\ValueObjects\Slug;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:seed-categories',
    description: 'Seed default categories (idempotent)'
)]
final class SeedCategoriesCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $repo = $this->em->getRepository(Category::class);

        $categories = [
            ['name' => 'PHP', 'slug' => 'php', 'sortOrder' => 1],
            ['name' => 'JavaScript', 'slug' => 'javascript', 'sortOrder' => 2],
            ['name' => 'Docker', 'slug' => 'docker', 'sortOrder' => 3],
            ['name' => 'Symfony', 'slug' => 'symfony', 'sortOrder' => 4],
            ['name' => 'PostgreSQL', 'slug' => 'postgresql', 'sortOrder' => 5],
            ['name' => 'Vue', 'slug' => 'vue', 'sortOrder' => 6],
        ];

        $created = 0;

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

            $this->em->persist($category);
            $created++;
        }

        $this->em->flush();

        $io->success(sprintf('Done. Created: %d', $created));

        return Command::SUCCESS;
    }
}
