<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200715204254 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE provider_category (provider_id INT NOT NULL, category_id INT NOT NULL, INDEX IDX_4E0E7728A53A8AA (provider_id), INDEX IDX_4E0E772812469DE2 (category_id), PRIMARY KEY(provider_id, category_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE provider_category ADD CONSTRAINT FK_4E0E7728A53A8AA FOREIGN KEY (provider_id) REFERENCES provider (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE provider_category ADD CONSTRAINT FK_4E0E772812469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('DROP TABLE provider_category');
    }
}
