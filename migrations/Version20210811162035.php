<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210811162035 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE show_category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE studio (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shows ADD category_id INT DEFAULT NULL, ADD studio_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE shows ADD CONSTRAINT FK_6C3BF14412469DE2 FOREIGN KEY (category_id) REFERENCES show_category (id)');
        $this->addSql('ALTER TABLE shows ADD CONSTRAINT FK_6C3BF144446F285F FOREIGN KEY (studio_id) REFERENCES studio (id)');
        $this->addSql('CREATE INDEX IDX_6C3BF14412469DE2 ON shows (category_id)');
        $this->addSql('CREATE INDEX IDX_6C3BF144446F285F ON shows (studio_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shows DROP FOREIGN KEY FK_6C3BF14412469DE2');
        $this->addSql('ALTER TABLE shows DROP FOREIGN KEY FK_6C3BF144446F285F');
        $this->addSql('DROP TABLE show_category');
        $this->addSql('DROP TABLE studio');
        $this->addSql('DROP INDEX IDX_6C3BF14412469DE2 ON shows');
        $this->addSql('DROP INDEX IDX_6C3BF144446F285F ON shows');
        $this->addSql('ALTER TABLE shows DROP category_id, DROP studio_id');
    }
}
