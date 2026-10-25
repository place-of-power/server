<?php

declare(strict_types=1);

namespace App\Tests\Feature\Presentation\Controller\Api\V1\Category;

use App\Domain\Types\ArticleStatus;
use App\Tests\Factory\ArticleFactory;
use App\Tests\Factory\CategoryFactory;
use App\Tests\Feature\ControllerTestCase;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class GetCategoryArticlesTest extends ControllerTestCase
{
    public function testReturnsPublishedArticlesWithPagination(): void
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $php = CategoryFactory::create($em, 'PHP', 'php', 0);
        ArticleFactory::create($em, $php, 'a1', 'A1', ArticleStatus::Published, new DateTimeImmutable('2026-01-01'));
        ArticleFactory::create($em, $php, 'a2', 'A2', ArticleStatus::Published, new DateTimeImmutable('2026-02-01'));

        $this->client->request('GET', $this->route('api_v1_categories_articles', ['slug' => 'php']).'?limit=1');

        self::assertResponseIsSuccessful();
        $body = $this->jsonResponse()['data'];

        self::assertCount(1, $body['items']);
        self::assertSame('a2', $body['items'][0]['slug']);
        self::assertNotEmpty($body['nextCursor']);

        // вторая страница по курсору
        $this->client->request('GET', $this->route('api_v1_categories_articles', ['slug' => 'php']).'?limit=1&cursor='.urlencode((string) $body['nextCursor']));

        self::assertResponseIsSuccessful();
        $page2 = $this->jsonResponse()['data'];

        self::assertSame('a1', $page2['items'][0]['slug']);
        self::assertNull($page2['nextCursor']);
    }

    public function testMissingCategoryReturns404(): void
    {
        $this->client->request('GET', $this->route('api_v1_categories_articles', ['slug' => 'nope']));

        self::assertResponseStatusCodeSame(404);
    }

    public function testInvalidLimitReturns422(): void
    {
        $this->client->request('GET', $this->route('api_v1_categories_articles', ['slug' => 'php']).'?limit=0');

        self::assertResponseStatusCodeSame(422);
    }

    public function testInvalidCursorReturns400(): void
    {
        $this->client->request('GET', $this->route('api_v1_categories_articles', ['slug' => 'php']).'?cursor=not-valid%21%21');

        self::assertResponseStatusCodeSame(400);
    }
}