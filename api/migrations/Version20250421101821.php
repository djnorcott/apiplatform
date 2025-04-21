<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250421101821 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE booking_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE customer_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE parking_space_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE SEQUENCE pricing_rule_id_seq INCREMENT BY 1 MINVALUE 1 START 1
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE booking (id INT NOT NULL, date_from VARCHAR(8) NOT NULL, date_to VARCHAR(8) NOT NULL, status VARCHAR(20) NOT NULL, total_price NUMERIC(10, 2) NOT NULL, customer_id INT NOT NULL, space_id INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E00CEDDE9395C3F3 ON booking (customer_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_E00CEDDE23575340 ON booking (space_id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE customer (id INT NOT NULL, name VARCHAR(100) NOT NULL, email VARCHAR(100) NOT NULL, phone VARCHAR(20) NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE parking_space (id INT NOT NULL, label VARCHAR(10) NOT NULL, is_active BOOLEAN NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE pricing_rule (id INT NOT NULL, name VARCHAR(50) NOT NULL, date_from VARCHAR(8) NOT NULL, date_to VARCHAR(8) NOT NULL, weekday_price NUMERIC(10, 2) NOT NULL, weekend_price NUMERIC(10, 2) NOT NULL, priority INT NOT NULL, PRIMARY KEY(id))
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE9395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking ADD CONSTRAINT FK_E00CEDDE23575340 FOREIGN KEY (space_id) REFERENCES parking_space (id) NOT DEFERRABLE INITIALLY IMMEDIATE
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP SEQUENCE booking_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE customer_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE parking_space_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            DROP SEQUENCE pricing_rule_id_seq CASCADE
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking DROP CONSTRAINT FK_E00CEDDE9395C3F3
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE booking DROP CONSTRAINT FK_E00CEDDE23575340
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE booking
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE customer
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE parking_space
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE pricing_rule
        SQL);
    }
}
