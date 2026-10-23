<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use App\Tests\Traits\RefreshDatabase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

abstract class ControllerTestCase extends WebTestCase
{
    use RefreshDatabase;

    protected KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->refreshDatabase();
    }

    /**
     * @param array<string, mixed> $parameters
     */
    protected function route(string $name, array $parameters = []): string
    {
        return $this->getContainer()->get(UrlGeneratorInterface::class)->generate($name, $parameters);
    }

    /**
     * @param array<string, mixed> $payload
     */
    protected function jsonRequest(string $method, string $uri, array $payload): void
    {
        $this->client->request(
            $method,
            $uri,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($payload, JSON_THROW_ON_ERROR),
        );
    }

    /**
     * @return array<string, mixed>
     */
    protected function jsonResponse(): array
    {
        $body = json_decode(
            (string) $this->client->getResponse()->getContent(),
            true,
            512,
            JSON_THROW_ON_ERROR,
        );

        return is_array($body) ? $body : [];
    }
}