<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200531234804 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE service_request_response_key (id INT AUTO_INCREMENT NOT NULL, service_request_id INT NOT NULL, service_response_key_id INT NOT NULL, response_key_value VARCHAR(255) NOT NULL, INDEX IDX_9C52C5CED42F8111 (service_request_id), INDEX IDX_9C52C5CE446E02F8 (service_response_key_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE service_request_response_key ADD CONSTRAINT FK_9C52C5CED42F8111 FOREIGN KEY (service_request_id) REFERENCES service_request (id)');
        $this->addSql('ALTER TABLE service_request_response_key ADD CONSTRAINT FK_9C52C5CE446E02F8 FOREIGN KEY (service_response_key_id) REFERENCES service_response_key (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE service_request_response_key');
    }
}
