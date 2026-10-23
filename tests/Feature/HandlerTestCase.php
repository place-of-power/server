<?php

declare(strict_types=1);

namespace App\Tests\Feature;

use App\Tests\Traits\RefreshDatabase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Messenger\MessageBusInterface;

abstract class HandlerTestCase extends KernelTestCase
{
    use RefreshDatabase;

    protected MessageBusInterface $messageBus;
    protected EntityManagerInterface $entityManager;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->messageBus = static::getContainer()->get(MessageBusInterface::class);
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);
        $this->refreshDatabase();
    }
}