<?php

declare(strict_types=1);

namespace App\Tests\Feature\Presentation\Controller\Api\V1\Article;

use App\Tests\Factory\CategoryFactory;
use App\Tests\Feature\ControllerTestCase;
use Doctrine\ORM\EntityManagerInterface;

final class CreateArticleTest extends ControllerTestCase
{
    public function testCreateReturns201WithArticle(): void
    {
        $category = CategoryFactory::create(
            static::getContainer()->get(EntityManagerInterface::class),
            'PHP',
            'php',
            0,
        );

        $this->jsonRequest('POST', $this->route('api_v1_articles_create'), [
            'slug' => 'php-article',
            'title' => 'PHP Article',
            'description' => 'Description',
            'body' => 'Body',
            'status' => 'draft',
            'categoryId' => $category->id()->value,
        ]);

        self::assertResponseStatusCodeSame(201);
        $article = $this->jsonResponse()['data'];

        self::assertSame('php-article', $article['slug']);
        self::assertSame('PHP Article', $article['title']);
        self::assertSame($category->id()->value, $article['categoryId']);
    }

    public function testCreateMissingCategoryReturns404(): void
    {
        $this->jsonRequest('POST', $this->route('api_v1_articles_create'), [
            'slug' => 'php-article',
            'title' => 'PHP Article',
            'description' => 'Description',
            'body' => 'Body',
            'status' => 'draft',
            'categoryId' => '00000000-0000-0000-0000-000000000000',
        ]);

        self::assertResponseStatusCodeSame(404);
    }

    public function testCreateInvalidPayloadReturns422(): void
    {
        $this->jsonRequest('POST', $this->route('api_v1_articles_create'), [
            'slug' => 'UPPERCASE',
            'title' => '',
            'description' => '',
            'body' => '',
            'status' => 'bogus',
            'categoryId' => '',
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertNull($this->jsonResponse()['data']);
        self::assertNotEmpty($this->jsonResponse()['errors']);
    }
}