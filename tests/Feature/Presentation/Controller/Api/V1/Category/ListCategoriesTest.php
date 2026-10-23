<?php

declare(strict_types=1);

namespace App\Tests\Feature\Presentation\Controller\Api\V1\Category;

use App\Domain\Entity\Category;
use App\Domain\ValueObjects\CategoryId;
use App\Domain\ValueObjects\Slug;
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
        $this->seedCategory('СУБД', 'databases', 30);
        $this->seedCategory('PHP', 'php', 10);
        $this->seedCategory('Архитектура', 'architecture', 20);

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

    private function seedCategory(string $name, string $slug, int $sortOrder): void
    {
        $manager = static::getContainer()->get(EntityManagerInterface::class);
        $manager->persist(Category::create(CategoryId::random(), $name, new Slug($slug), $sortOrder));
        $manager->flush();
    }
}