<?php

declare(strict_types=1);

namespace App\Tests\Feature\Presentation\Controller\Api\V1\Category;

use App\Domain\Entity\Category;
use App\Tests\Factory\CategoryFactory;
use App\Tests\Feature\ControllerTestCase;
use Doctrine\ORM\EntityManagerInterface;

final class DeleteCategoryTest extends ControllerTestCase
{
    public function testDeleteRemovesCategory(): void
    {
        $category = $this->seed('PHP', 'php', 0);

        $this->client->request('DELETE', $this->route('api_v1_categories_delete', ['id' => $category->id()->value]));
        self::assertResponseIsSuccessful();
        self::assertNull($this->jsonResponse()['data']);
    }

    public function testDeleteMissingCategoryReturns404(): void
    {
        $this->client->request('DELETE', $this->route('api_v1_categories_delete', ['id' => '00000000-0000-0000-0000-000000000000']));

        self::assertResponseStatusCodeSame(404);
        self::assertNotEmpty($this->jsonResponse()['errors']);
    }

    public function testDeleteInvalidIdReturns404(): void
    {
        $this->client->request('DELETE', $this->route('api_v1_categories_delete', ['id' => 'not-a-uuid']));

        self::assertResponseStatusCodeSame(404);
        self::assertNotEmpty($this->jsonResponse()['errors']);
    }

    private function seed(string $name, string $slug, int $sortOrder): Category
    {
        return CategoryFactory::create(
            static::getContainer()->get(EntityManagerInterface::class),
            $name,
            $slug,
            $sortOrder,
        );
    }
}