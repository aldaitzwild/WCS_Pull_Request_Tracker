<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230503131203 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE project_contributor (project_id INT NOT NULL, contributor_id INT NOT NULL, INDEX IDX_12A06068166D1F9C (project_id), INDEX IDX_12A060687A19A357 (contributor_id), PRIMARY KEY(project_id, contributor_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE project_contributor ADD CONSTRAINT FK_12A06068166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_contributor ADD CONSTRAINT FK_12A060687A19A357 FOREIGN KEY (contributor_id) REFERENCES contributor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE contributor DROP FOREIGN KEY FK_DA6F9793166D1F9C');
        $this->addSql('DROP INDEX IDX_DA6F9793166D1F9C ON contributor');
        $this->addSql('ALTER TABLE contributor DROP project_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE project_contributor DROP FOREIGN KEY FK_12A06068166D1F9C');
        $this->addSql('ALTER TABLE project_contributor DROP FOREIGN KEY FK_12A060687A19A357');
        $this->addSql('DROP TABLE project_contributor');
        $this->addSql('ALTER TABLE contributor ADD project_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE contributor ADD CONSTRAINT FK_DA6F9793166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_DA6F9793166D1F9C ON contributor (project_id)');
    }
}
