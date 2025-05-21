<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250521105345 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE log (id INT AUTO_INCREMENT NOT NULL, service_name VARCHAR(255) NOT NULL, timestamp DATETIME NOT NULL, request_line VARCHAR(255) NOT NULL, status_code INT NOT NULL, INDEX idx_log_service_name (service_name), INDEX idx_log_timestamp (timestamp), INDEX idx_log_status_code (status_code), INDEX idx_log_timestamp_service (timestamp, service_name), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE log
        SQL);
    }
}
