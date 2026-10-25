<?php

declare(strict_types=1);

namespace App\Tests\Feature\Presentation\Controller\Api\V1\Article;

use App\Domain\Types\ArticleStatus;
use App\Tests\Factory\ArticleFactory;
use App\Tests\Factory\CategoryFactory;
use App\Tests\Feature\ControllerTestCase;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class GetRecentArticlesTest extends ControllerTestCase
{
    public function testRecentReturnsPublishedArticlesDesc(): void
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $category = CategoryFactory::create($em, 'PHP', 'php', 0);
        ArticleFactory::create($em, $category, 'old', 'Олд', ArticleStatus::Published, new DateTimeImmutable('2026-01-01'));
        ArticleFactory::create($em, $category, 'new', 'Новый', ArticleStatus::Published, new DateTimeImmutable('2026-10-01'));
        ArticleFactory::create($em, $category, 'draft', 'Черновик', ArticleStatus::Draft, new DateTimeImmutable('2026-12-01'));

        $this->client->request('GET', $this->route('api_v1_articles_recent'));

        self::assertResponseIsSuccessful();
        $data = $this->jsonResponse()['data'];

        self::assertCount(2, $data);
        self::assertSame(['new', 'old'], array_column($data, 'slug'));
    }

    public function testRecentRespectsLimit(): void
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $category = CategoryFactory::create($em, 'PHP', 'php', 0);
        ArticleFactory::create($em, $category, 'a1', 'A1', ArticleStatus::Published, new DateTimeImmutable('2026-01-01'));
        ArticleFactory::create($em, $category, 'b2', 'B2', ArticleStatus::Published, new DateTimeImmutable('2026-02-01'));
        ArticleFactory::create($em, $category, 'c3', 'C3', ArticleStatus::Published, new DateTimeImmutable('2026-03-01'));

        $this->client->request('GET', $this->route('api_v1_articles_recent').'?limit=2');

        self::assertResponseIsSuccessful();
        self::assertCount(2, $this->jsonResponse()['data']);
    }

    public function testRecentInvalidLimitReturns422(): void
    {
        $this->client->request('GET', $this->route('api_v1_articles_recent').'?limit=0');

        self::assertResponseStatusCodeSame(422);
    }
}