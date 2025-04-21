<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250421113347 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE booking DROP CONSTRAINT fk_e00cedde23575340
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX idx_e00cedde23575340
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking RENAME COLUMN space_id TO parking_space_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE45DF8272 FOREIGN KEY (parking_space_id) REFERENCES parking_space (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E00CEDDE45DF8272 ON booking (parking_space_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE booking DROP CONSTRAINT FK_E00CEDDE45DF8272
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_E00CEDDE45DF8272
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking RENAME COLUMN parking_space_id TO space_id
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking ADD CONSTRAINT fk_e00cedde23575340 FOREIGN KEY (space_id) REFERENCES parking_space (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX idx_e00cedde23575340 ON booking (space_id)
        SQL);
    }
}
