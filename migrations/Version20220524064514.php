<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220524064514 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipes ADD pseudo_id INT NOT NULL, ADD date DATETIME NOT NULL');
        $this->addSql('ALTER TABLE recipes ADD CONSTRAINT FK_A369E2B520E394C2 FOREIGN KEY (pseudo_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_A369E2B520E394C2 ON recipes (pseudo_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE recipes DROP FOREIGN KEY FK_A369E2B520E394C2');
        $this->addSql('DROP INDEX IDX_A369E2B520E394C2 ON recipes');
        $this->addSql('ALTER TABLE recipes DROP pseudo_id, DROP date');
    }
}
