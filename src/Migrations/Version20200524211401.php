<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200524211401 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, username VARCHAR(255) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, date_updated DATETIME NOT NULL, date_added DATETIME NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE api_token (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, token VARCHAR(255) NOT NULL, expires_at DATETIME NOT NULL, INDEX IDX_7BA2F5EBA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_parameter (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, parameter_id INT NOT NULL, INDEX IDX_7206AB5AED5CA9E6 (service_id), INDEX IDX_7206AB5A7C56DBD6 (parameter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE oauth_access_tokens (id INT AUTO_INCREMENT NOT NULL, provider_id INT NOT NULL, access_token LONGTEXT NOT NULL, expiry DATETIME NOT NULL, date_added DATETIME NOT NULL, INDEX IDX_CA42527CA53A8AA (provider_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, provider_id INT NOT NULL, service_name VARCHAR(255) NOT NULL, service_label VARCHAR(255) NOT NULL, INDEX IDX_E19D9AD2A53A8AA (provider_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE provider (id INT AUTO_INCREMENT NOT NULL, category_id INT NOT NULL, provider_name VARCHAR(255) NOT NULL, provider_api_base_url VARCHAR(255) NOT NULL, provider_access_key VARCHAR(255) NOT NULL, provider_secret_key VARCHAR(255) NOT NULL, provider_user_id VARCHAR(255) NOT NULL, date_updated DATETIME NOT NULL, date_added DATETIME NOT NULL, INDEX IDX_92C4739C12469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE provider_property (id INT AUTO_INCREMENT NOT NULL, provider_id INT NOT NULL, property_id INT NOT NULL, property_value VARCHAR(255) NOT NULL, INDEX IDX_C3B07237A53A8AA (provider_id), INDEX IDX_C3B07237549213EC (property_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE category (id INT AUTO_INCREMENT NOT NULL, category_name VARCHAR(255) NOT NULL, category_label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE property (id INT AUTO_INCREMENT NOT NULL, property_name VARCHAR(255) NOT NULL, property_label VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE parameter (id INT AUTO_INCREMENT NOT NULL, parameter_name VARCHAR(255) NOT NULL, parameter_value VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE api_token ADD CONSTRAINT FK_7BA2F5EBA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE service_parameter ADD CONSTRAINT FK_7206AB5AED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE service_parameter ADD CONSTRAINT FK_7206AB5A7C56DBD6 FOREIGN KEY (parameter_id) REFERENCES parameter (id)');
        $this->addSql('ALTER TABLE oauth_access_tokens ADD CONSTRAINT FK_CA42527CA53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id)');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id)');
        $this->addSql('ALTER TABLE provider ADD CONSTRAINT FK_92C4739C12469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE provider_property ADD CONSTRAINT FK_C3B07237A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id)');
        $this->addSql('ALTER TABLE provider_property ADD CONSTRAINT FK_C3B07237549213EC FOREIGN KEY (property_id) REFERENCES property (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE api_token DROP FOREIGN KEY FK_7BA2F5EBA76ED395');
        $this->addSql('ALTER TABLE service_parameter DROP FOREIGN KEY FK_7206AB5AED5CA9E6');
        $this->addSql('ALTER TABLE oauth_access_tokens DROP FOREIGN KEY FK_CA42527CA53A8AA');
        $this->addSql('ALTER TABLE service DROP FOREIGN KEY FK_E19D9AD2A53A8AA');
        $this->addSql('ALTER TABLE provider_property DROP FOREIGN KEY FK_C3B07237A53A8AA');
        $this->addSql('ALTER TABLE provider DROP FOREIGN KEY FK_92C4739C12469DE2');
        $this->addSql('ALTER TABLE provider_property DROP FOREIGN KEY FK_C3B07237549213EC');
        $this->addSql('ALTER TABLE service_parameter DROP FOREIGN KEY FK_7206AB5A7C56DBD6');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE api_token');
        $this->addSql('DROP TABLE service_parameter');
        $this->addSql('DROP TABLE oauth_access_tokens');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE provider');
        $this->addSql('DROP TABLE provider_property');
        $this->addSql('DROP TABLE category');
        $this->addSql('DROP TABLE property');
        $this->addSql('DROP TABLE parameter');
    }
}
