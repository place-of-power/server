<?php

declare(strict_types=1);

namespace App\Infrastructure\Console;

use App\Domain\Contracts\Repositories\ArticleRepository;
use App\Domain\Contracts\Repositories\CategoryRepository;
use App\Domain\Entity\Article;
use App\Domain\Entity\Category;
use App\Domain\Types\ArticleStatus;
use App\Domain\ValueObjects\ArticleId;
use App\Domain\ValueObjects\CategoryId;
use App\Domain\ValueObjects\Slug;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'app:seed', description: 'Наполняет базу категориями и примерными статьями (идемпотентно)')]
final class SeedCommand extends Command
{
    public function __construct(
        private readonly CategoryRepository $categoryRepository,
        private readonly ArticleRepository $articleRepository,
        private readonly EntityManagerInterface $em,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $categories = [
            ['slug' => 'php', 'name' => 'PHP', 'sort' => 1],
            ['slug' => 'javascript', 'name' => 'JavaScript', 'sort' => 2],
            ['slug' => 'docker', 'name' => 'Docker', 'sort' => 3],
            ['slug' => 'symfony', 'name' => 'Symfony', 'sort' => 4],
            ['slug' => 'postgresql', 'name' => 'PostgreSQL', 'sort' => 5],
            ['slug' => 'vue', 'name' => 'Vue', 'sort' => 6],
        ];

        $saved = [];

        foreach ($categories as $item) {
            $existing = $this->categoryRepository->findBySlug(new Slug($item['slug']));

            if ($existing !== null) {
                $saved[$item['slug']] = $existing;
                continue;
            }

            $category = Category::create(
                new CategoryId(CategoryId::random()->value),
                $item['name'],
                new Slug($item['slug']),
                $item['sort'],
            );
            $this->categoryRepository->save($category);
            $saved[$item['slug']] = $category;
        }

        $now = new DateTimeImmutable('2026-10-12 10:00:00', new DateTimeZone('Europe/Moscow'));

        $articles = [
            [
                'slug' => 'full-text-search-postgresql',
                'title' => 'Как устроен полнотекстовый поиск в PostgreSQL',
                'description' => 'Разбираем tsvector, GIN-индексы и кастомный русско-английский словарь.',
                'body' => "## Как работает tsvector\n\nПолнотекстовый поиск часто сводят к LIKE-запросу, но уже на тысячах документов это перестаёт работать.",
                'category' => 'postgresql',
            ],
            [
                'slug' => 'docker-volumes',
                'title' => 'Тома в Docker: от bind mount до named volume',
                'description' => 'Чем отличаются bind mount, named volume и tmpfs.',
                'body' => "## Три способа хранить данные\n\nDocker предлагает bind mount, named volume и tmpfs.",
                'category' => 'docker',
            ],
            [
                'slug' => 'php-readonly-value-objects',
                'title' => 'Readonly value objects в PHP',
                'description' => 'Инкапсуляция правил в небольших неизменяемых объектах.',
                'body' => "## Проблема примитивов\n\nСтроки и числа не несут правил: валидацию приходится повторять.",
                'category' => 'php',
            ],
        ];

        foreach ($articles as $item) {
            $category = $saved[$item['category']] ?? null;

            if ($category === null) {
                continue;
            }

            $existing = $this->articleRepository->findBySlug(new Slug($item['slug']));

            if ($existing !== null) {
                continue;
            }

            $article = Article::create(
                id: new ArticleId(ArticleId::random()->value),
                slug: new Slug($item['slug']),
                title: $item['title'],
                description: $item['description'],
                body: $item['body'],
                status: ArticleStatus::Draft,
                category: $category,
                now: $now,
            );

            $article->publish($now);
            $this->articleRepository->save($article);
        }

        $this->em->flush();

        $io->success('Сид применён.');

        return Command::SUCCESS;
    }
}