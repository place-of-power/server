<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\UniqueConstraint;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20261016084501 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создание таблицы categories';
    }

    public function up(Schema $schema): void
    {
        $categoriesTable = Table::editor()
            ->setUnquotedName('categories')
            ->addColumn(
                Column::editor()
                    ->setUnquotedName('id')
                    ->setTypeName(Types::GUID)
                    ->create(),
            )
            ->addColumn(
                Column::editor()
                    ->setUnquotedName('name')
                    ->setTypeName(Types::STRING)
                    ->setLength(255)
                    ->create(),
            )
            ->addColumn(
                Column::editor()
                    ->setUnquotedName('slug')
                    ->setTypeName(Types::STRING)
                    ->setLength(255)
                    ->create(),
            )
            ->addColumn(
                Column::editor()
                    ->setUnquotedName('sort_order')
                    ->setTypeName(Types::INTEGER)
                    ->create(),
            )
            ->addColumn(
                Column::editor()
                    ->setUnquotedName('created_at')
                    ->setTypeName(Types::DATETIME_IMMUTABLE)
                    ->create(),
            )
            ->addColumn(
                Column::editor()
                    ->setUnquotedName('updated_at')
                    ->setTypeName(Types::DATETIME_IMMUTABLE)
                    ->create(),
            )
            ->addPrimaryKeyConstraint(
                PrimaryKeyConstraint::editor()
                    ->setUnquotedName('categories_pk')
                    ->setUnquotedColumnNames('id')
                    ->create(),
            )
            ->addUniqueConstraint(
                UniqueConstraint::editor()
                    ->setUnquotedName('uq_categories_name')
                    ->setUnquotedColumnNames('name')
                    ->create(),
            )
            ->addUniqueConstraint(
                UniqueConstraint::editor()
                    ->setUnquotedName('uq_categories_slug')
                    ->setUnquotedColumnNames('slug')
                    ->create(),
            )
            ->create();

        $newSchema = Schema::editor()
            ->setTables($categoriesTable)
            ->create();

        foreach ($newSchema->toSql($this->platform) as $sql) {
            $this->addSql($sql);
        }
    }

    public function down(Schema $schema): void
    {
        $newSchema = Schema::editor()
            ->setTables($schema->getTable('categories'))
            ->create();

        foreach ($newSchema->toDropSql($this->platform) as $sql) {
            $this->addSql($sql);
        }
    }
}
