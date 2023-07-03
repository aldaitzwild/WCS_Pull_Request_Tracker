<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230627094747 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE note DROP name');
        $this->addSql('ALTER TABLE pull_request DROP FOREIGN KEY FK_8B9B9EEF7A19A357');
        $this->addSql('ALTER TABLE pull_request CHANGE contributor_id contributor_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE pull_request ADD CONSTRAINT FK_8B9B9EEF7A19A357 FOREIGN KEY (contributor_id) REFERENCES contributor (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE note ADD name VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE pull_request DROP FOREIGN KEY FK_8B9B9EEF7A19A357');
        $this->addSql('ALTER TABLE pull_request CHANGE contributor_id contributor_id INT NOT NULL');
        $this->addSql('ALTER TABLE pull_request ADD CONSTRAINT FK_8B9B9EEF7A19A357 FOREIGN KEY (contributor_id) REFERENCES contributor (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
    }
}
