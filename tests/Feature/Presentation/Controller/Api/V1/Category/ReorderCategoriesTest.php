<?php

declare(strict_types=1);

namespace App\Tests\Feature\Presentation\Controller\Api\V1\Category;

use App\Tests\Feature\ControllerTestCase;

final class ReorderCategoriesTest extends ControllerTestCase
{
    public function testReorderReturnsSuccess(): void
    {
        $items = [
            $this->createItem('PHP', 'php'),
            $this->createItem('СУБД', 'databases'),
            $this->createItem('Архитектура', 'architecture'),
        ];

        // инвертируем порядок, каждому item задаём sortOrder по новой позиции
        $reordered = array_reverse($items);
        $payload = array_map(
            static fn (array $item, int $index): array => ['id' => $item['id'], 'sortOrder' => $index],
            $reordered,
            array_keys($reordered),
        );

        $this->jsonRequest('PUT', $this->route('api_v1_categories_reorder'), ['items' => $payload]);

        self::assertResponseIsSuccessful();
        self::assertNull($this->jsonResponse()['data']);
    }

    public function testReorderEmptyListReturns422(): void
    {
        $this->jsonRequest('PUT', $this->route('api_v1_categories_reorder'), ['items' => []]);

        self::assertResponseStatusCodeSame(422);
    }

    public function testReorderMissingCategoryReturns404(): void
    {
        $this->jsonRequest('PUT', $this->route('api_v1_categories_reorder'), [
            'items' => [['id' => '00000000-0000-0000-0000-000000000000', 'sortOrder' => 0]],
        ]);

        self::assertResponseStatusCodeSame(404);
    }

    /**
     * @return array{id: string}
     */
    private function createItem(string $name, string $slug): array
    {
        $this->jsonRequest('POST', $this->route('api_v1_categories_create'), [
            'name' => $name,
            'slug' => $slug,
            'sortOrder' => 0,
        ]);

        return ['id' => $this->jsonResponse()['data']['id']];
    }
}