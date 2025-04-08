<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250408114909 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Add as nullable first
        $this->addSql('ALTER TABLE notification ADD digest BOOLEAN DEFAULT FALSE');
        $this->addSql('ALTER TABLE notification ADD sent BOOLEAN DEFAULT FALSE');

        // Fill existing rows
        $this->addSql('UPDATE notification SET digest = FALSE WHERE digest IS NULL');
        $this->addSql('UPDATE notification SET sent = FALSE WHERE sent IS NULL');

        // Make NOT NULL
        $this->addSql('ALTER TABLE notification ALTER COLUMN digest SET NOT NULL');
        $this->addSql('ALTER TABLE notification ALTER COLUMN sent SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP digest
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification DROP sent
        SQL);
    }
}
