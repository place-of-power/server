<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\ForeignKeyConstraint;
use Doctrine\DBAL\Schema\Index;
use Doctrine\DBAL\Schema\PrimaryKeyConstraint;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Schema\UniqueConstraint;
use Doctrine\DBAL\Types\Types;
use Doctrine\Migrations\AbstractMigration;

final class Version20261016084516 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Создание таблицы articles';
    }

    public function up(Schema $schema): void
    {
        $articlesTable = Table::editor()
            ->setUnquotedName('articles')
            ->addColumn(
                Column::editor()
                    ->setUnquotedName('id')
                    ->setTypeName(Types::GUID)
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
                    ->setUnquotedName('title')
                    ->setTypeName(Types::STRING)
                    ->setLength(255)
                    ->create(),
            )
            ->addColumn(
                Column::editor()
                    ->setUnquotedName('body')
                    ->setTypeName(Types::TEXT)
                    ->create(),
            )
            ->addColumn(
                Column::editor()
                    ->setUnquotedName('description')
                    ->setTypeName(Types::TEXT)
                    ->create(),
            )
            ->addColumn(
                Column::editor()
                    ->setUnquotedName('status')
                    ->setTypeName(Types::STRING)
                    ->setLength(255)
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
            ->addColumn(
                Column::editor()
                    ->setUnquotedName('published_at')
                    ->setTypeName(Types::DATETIME_IMMUTABLE)
                    ->setNotNull(false)
                    ->create(),
            )
            ->addColumn(
                Column::editor()
                    ->setUnquotedName('category_id')
                    ->setTypeName(Types::GUID)
                    ->create(),
            )
            ->addPrimaryKeyConstraint(
                PrimaryKeyConstraint::editor()
                    ->setUnquotedName('articles_pk')
                    ->setUnquotedColumnNames('id')
                    ->create(),
            )
            ->addUniqueConstraint(
                UniqueConstraint::editor()
                    ->setUnquotedName('uq_articles_slug')
                    ->setUnquotedColumnNames('slug')
                    ->create(),
            )
            ->addIndex(
                Index::editor()
                    ->setUnquotedName('idx_articles_category')
                    ->setUnquotedColumnNames('category_id')
                    ->create(),
            )
            ->addForeignKeyConstraint(
                ForeignKeyConstraint::editor()
                    ->setUnquotedName('fk_articles_category')
                    ->setUnquotedReferencingColumnNames('category_id')
                    ->setUnquotedReferencedTableName('categories')
                    ->setUnquotedReferencedColumnNames('id')
                    ->create(),
            )
            ->create();

        $newSchema = Schema::editor()
            ->setTables($articlesTable)
            ->create();

        foreach ($newSchema->toSql($this->platform) as $sql) {
            $this->addSql($sql);
        }
    }

    public function down(Schema $schema): void
    {
        $newSchema = Schema::editor()
            ->setTables($schema->getTable('articles'))
            ->create();

        foreach ($newSchema->toDropSql($this->platform) as $sql) {
            $this->addSql($sql);
        }
    }
}
