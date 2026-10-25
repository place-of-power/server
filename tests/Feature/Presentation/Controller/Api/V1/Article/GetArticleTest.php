<?php

declare(strict_types=1);

namespace App\Tests\Feature\Presentation\Controller\Api\V1\Article;

use App\Domain\Types\ArticleStatus;
use App\Tests\Factory\ArticleFactory;
use App\Tests\Factory\CategoryFactory;
use App\Tests\Feature\ControllerTestCase;
use Doctrine\ORM\EntityManagerInterface;

final class GetArticleTest extends ControllerTestCase
{
    public function testReturnsArticleBySlug(): void
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        $category = CategoryFactory::create($em, 'PHP', 'php', 0);
        ArticleFactory::create($em, $category, 'php-article', 'PHP Article', ArticleStatus::Published);

        $this->client->request('GET', $this->route('api_v1_articles_show', ['slug' => 'php-article']));

        self::assertResponseIsSuccessful();
        $article = $this->jsonResponse()['data'];

        self::assertSame('php-article', $article['slug']);
        self::assertSame('PHP Article', $article['title']);
        self::assertSame($category->id()->value, $article['categoryId']);
    }

    public function testMissingArticleReturns404(): void
    {
        $this->client->request('GET', $this->route('api_v1_articles_show', ['slug' => 'nope']));

        self::assertResponseStatusCodeSame(404);
        self::assertNotEmpty($this->jsonResponse()['errors']);
    }
}