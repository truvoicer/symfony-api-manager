<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200528070623 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE service_parameter DROP FOREIGN KEY FK_7206AB5A7C56DBD6');
        $this->addSql('ALTER TABLE service_parameter DROP FOREIGN KEY FK_7206AB5AED5CA9E6');
        $this->addSql('DROP TABLE parameter');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE service_parameter');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE parameter (id INT AUTO_INCREMENT NOT NULL, parameter_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, parameter_value VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, provider_id INT NOT NULL, service_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, service_label VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_E19D9AD2A53A8AA (provider_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE service_parameter (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, parameter_id INT NOT NULL, INDEX IDX_7206AB5A7C56DBD6 (parameter_id), INDEX IDX_7206AB5AED5CA9E6 (service_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE service ADD CONSTRAINT FK_E19D9AD2A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE service_parameter ADD CONSTRAINT FK_7206AB5A7C56DBD6 FOREIGN KEY (parameter_id) REFERENCES parameter (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('ALTER TABLE service_parameter ADD CONSTRAINT FK_7206AB5AED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
