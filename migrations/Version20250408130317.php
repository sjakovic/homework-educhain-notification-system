<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250408130317 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE notification_preference (id SERIAL NOT NULL, user_id INT NOT NULL, type VARCHAR(100) NOT NULL, channel VARCHAR(50) NOT NULL, frequency VARCHAR(50) NOT NULL, enabled BOOLEAN NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ALTER digest DROP DEFAULT
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ALTER sent DROP DEFAULT
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SCHEMA public
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE notification_preference
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ALTER digest SET DEFAULT false
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE notification ALTER sent SET DEFAULT false
        SQL);
    }
}
