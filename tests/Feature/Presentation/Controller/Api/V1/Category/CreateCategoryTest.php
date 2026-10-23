<?php

declare(strict_types=1);

namespace App\Tests\Feature\Presentation\Controller\Api\V1\Category;

use App\Tests\Feature\ControllerTestCase;

final class CreateCategoryTest extends ControllerTestCase
{
    public function testCreateReturns201WithCategory(): void
    {
        $this->jsonRequest('POST', $this->route('api_v1_categories_create'), [
            'name' => 'Архитектура',
            'slug' => 'architecture',
            'sortOrder' => 10,
        ]);

        self::assertResponseStatusCodeSame(201);
        $created = $this->jsonResponse()['data'];

        self::assertSame('architecture', $created['slug']);
        self::assertSame('Архитектура', $created['name']);
    }

    public function testCreateThenListRoundTrip(): void
    {
        $this->jsonRequest('POST', $this->route('api_v1_categories_create'), [
            'name' => 'Архитектура',
            'slug' => 'architecture',
            'sortOrder' => 10,
        ]);
        self::assertResponseStatusCodeSame(201);

        $this->client->request('GET', $this->route('api_v1_categories'));
        self::assertResponseIsSuccessful();
        self::assertCount(1, $this->jsonResponse()['data']);
        self::assertSame('Архитектура', $this->jsonResponse()['data'][0]['name']);
    }

    public function testCreateDuplicateSlugReturns409(): void
    {
        $this->jsonRequest('POST', $this->route('api_v1_categories_create'), ['name' => 'PHP', 'slug' => 'php', 'sortOrder' => 0]);
        self::assertResponseStatusCodeSame(201);

        $this->jsonRequest('POST', $this->route('api_v1_categories_create'), [
            'name' => 'Laravel',
            'slug' => 'php',
            'sortOrder' => 1,
        ]);
        self::assertResponseStatusCodeSame(409);
    }

    public function testCreateDuplicateNameReturns409(): void
    {
        $this->jsonRequest('POST', $this->route('api_v1_categories_create'), ['name' => 'PHP', 'slug' => 'php', 'sortOrder' => 0]);
        self::assertResponseStatusCodeSame(201);

        $this->jsonRequest('POST', $this->route('api_v1_categories_create'), [
            'name' => 'PHP',
            'slug' => 'another-slug',
            'sortOrder' => 1,
        ]);
        self::assertResponseStatusCodeSame(409);
    }

    public function testCreateInvalidPayloadReturns422(): void
    {
        $this->jsonRequest('POST', $this->route('api_v1_categories_create'), [
            'name' => '',
            'slug' => 'UPPERCASE',
            'sortOrder' => -1,
        ]);

        self::assertResponseStatusCodeSame(422);
        self::assertNull($this->jsonResponse()['data']);
        self::assertNotEmpty($this->jsonResponse()['errors']);
    }
}