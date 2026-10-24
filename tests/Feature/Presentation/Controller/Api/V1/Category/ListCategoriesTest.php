<?php

declare(strict_types=1);

namespace App\Tests\Feature\Presentation\Controller\Api\V1\Category;

use App\Tests\Factory\CategoryFactory;
use App\Tests\Feature\ControllerTestCase;
use Doctrine\ORM\EntityManagerInterface;

final class ListCategoriesTest extends ControllerTestCase
{
    public function testListReturnsEmptyDataWhenNoCategories(): void
    {
        $this->client->request('GET', $this->route('api_v1_categories'));

        self::assertResponseIsSuccessful();
        $body = $this->jsonResponse();

        self::assertSame([], $body['errors']);
        self::assertSame([], $body['data']);
    }

    public function testListReturnsCategoriesOrderedBySortOrder(): void
    {
        $em = static::getContainer()->get(EntityManagerInterface::class);
        CategoryFactory::create($em, 'СУБД', 'databases', 30);
        CategoryFactory::create($em, 'PHP', 'php', 10);
        CategoryFactory::create($em, 'Архитектура', 'architecture', 20);

        $this->client->request('GET', $this->route('api_v1_categories'));

        self::assertResponseIsSuccessful();
        $data = $this->jsonResponse()['data'];

        self::assertCount(3, $data);
        self::assertSame(['PHP', 'Архитектура', 'СУБД'], array_column($data, 'name'));
    }

    public function testInvalidLimitReturns422(): void
    {
        $this->client->request('GET', $this->route('api_v1_categories').'?limit=0');

        self::assertResponseStatusCodeSame(422);
        self::assertNull($this->jsonResponse()['data']);
        self::assertNotEmpty($this->jsonResponse()['errors']);
    }
}