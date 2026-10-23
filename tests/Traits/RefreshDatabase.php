<?php

declare(strict_types=1);

namespace App\Tests\Traits;

use Doctrine\ORM\EntityManagerInterface;

trait RefreshDatabase
{
    protected function refreshDatabase(): void
    {
        $manager = static::getContainer()->get(EntityManagerInterface::class);
        $connection = $manager->getConnection();

        $tables = array_filter(
            $connection->createSchemaManager()->listTableNames(),
            static fn (string $table): bool => 'doctrine_migration_versions' !== $table,
        );

        if ([] === $tables) {
            return;
        }

        $connection->executeStatement(sprintf(
            'TRUNCATE TABLE %s RESTART IDENTITY CASCADE',
            implode(', ', array_map(static fn (string $t): string => '"'.$t.'"', $tables)),
        ));
    }
}