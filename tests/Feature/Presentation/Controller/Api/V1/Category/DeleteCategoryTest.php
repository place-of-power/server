<?php

declare(strict_types=1);

namespace App\Tests\Feature\Presentation\Controller\Api\V1\Category;

use App\Tests\Feature\ControllerTestCase;

final class DeleteCategoryTest extends ControllerTestCase
{
    public function testDeleteRemovesCategory(): void
    {
        $this->jsonRequest('POST', $this->route('api_v1_categories_create'), [
            'name' => 'PHP',
            'slug' => 'php',
            'sortOrder' => 0,
        ]);
        $id = $this->jsonResponse()['data']['id'];

        $this->client->request('DELETE', $this->route('api_v1_categories_delete', ['id' => $id]));
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
}