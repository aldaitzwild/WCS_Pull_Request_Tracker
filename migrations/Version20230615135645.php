<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230615135645 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pull_request DROP FOREIGN KEY FK_8B9B9EEF166D1F9C');
        $this->addSql('ALTER TABLE pull_request CHANGE contributor_id contributor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pull_request ADD CONSTRAINT FK_8B9B9EEF166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE pull_request DROP FOREIGN KEY FK_8B9B9EEF166D1F9C');
        $this->addSql('ALTER TABLE pull_request CHANGE contributor_id contributor_id INT NOT NULL');
        $this->addSql('ALTER TABLE pull_request ADD CONSTRAINT FK_8B9B9EEF166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON UPDATE NO ACTION ON DELETE CASCADE');
    }
}
