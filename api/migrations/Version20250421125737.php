<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250421125737 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE daily_price (date VARCHAR(8) NOT NULL, price NUMERIC(10, 2) NOT NULL, pricing_rule_id INT NOT NULL, PRIMARY KEY(date))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_4AE9430DB5B58DBB ON daily_price (pricing_rule_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE daily_price ADD CONSTRAINT FK_4AE9430DB5B58DBB FOREIGN KEY (pricing_rule_id) REFERENCES pricing_rule (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE daily_price DROP CONSTRAINT FK_4AE9430DB5B58DBB
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE daily_price
        SQL);
    }
}
