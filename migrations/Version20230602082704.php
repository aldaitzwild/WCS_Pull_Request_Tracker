<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230602082704 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE pull_request (id INT AUTO_INCREMENT NOT NULL, project_id INT NOT NULL, contributor_id INT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', is_merged TINYINT(1) NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_8B9B9EEF166D1F9C (project_id), INDEX IDX_8B9B9EEF7A19A357 (contributor_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE pull_request ADD CONSTRAINT FK_8B9B9EEF166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE pull_request ADD CONSTRAINT FK_8B9B9EEF7A19A357 FOREIGN KEY (contributor_id) REFERENCES contributor (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pull_request DROP FOREIGN KEY FK_8B9B9EEF166D1F9C');
        $this->addSql('ALTER TABLE pull_request DROP FOREIGN KEY FK_8B9B9EEF7A19A357');
        $this->addSql('DROP TABLE pull_request');
    }
}
