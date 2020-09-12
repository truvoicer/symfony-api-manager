<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200529011515 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE service_response_key (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, key_name VARCHAR(255) NOT NULL, key_value VARCHAR(255) NOT NULL, INDEX IDX_48CF0467ED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_request_parameter (id INT AUTO_INCREMENT NOT NULL, service_request_id INT NOT NULL, parameter_name VARCHAR(255) NOT NULL, parameter_value VARCHAR(255) NOT NULL, INDEX IDX_B710562D42F8111 (service_request_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, service_name VARCHAR(255) NOT NULL, service_label VARCHAR(255) NOT NULL, INDEX IDX_E19D9AD212469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_request (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, provider_id INT NOT NULL, service_request_name VARCHAR(255) NOT NULL, service_request_label VARCHAR(255) NOT NULL, INDEX IDX_F413DD03ED5CA9E6 (service_id), INDEX IDX_F413DD03A53A8AA (provider_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE service_response_key ADD CONSTRAINT FK_48CF0467ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE service_request_parameter ADD CONSTRAINT FK_B710562D42F8111 FOREIGN KEY (service_request_id) REFERENCES service_request (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD212469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE service_request ADD CONSTRAINT FK_F413DD03ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE service_request ADD CONSTRAINT FK_F413DD03A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE service_response_key DROP FOREIGN KEY FK_48CF0467ED5CA9E6');
        $this->addSql('ALTER TABLE service_request DROP FOREIGN KEY FK_F413DD03ED5CA9E6');
        $this->addSql('ALTER TABLE service_request_parameter DROP FOREIGN KEY FK_B710562D42F8111');
        $this->addSql('DROP TABLE service_response_key');
        $this->addSql('DROP TABLE service_request_parameter');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE service_request');
    }
}
