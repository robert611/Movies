<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210811164949 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE show_theme (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE shows_show_theme (shows_id INT NOT NULL, show_theme_id INT NOT NULL, INDEX IDX_44B0159DAD7ED998 (shows_id), INDEX IDX_44B0159D60669568 (show_theme_id), PRIMARY KEY(shows_id, show_theme_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE shows_show_theme ADD CONSTRAINT FK_44B0159DAD7ED998 FOREIGN KEY (shows_id) REFERENCES shows (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE shows_show_theme ADD CONSTRAINT FK_44B0159D60669568 FOREIGN KEY (show_theme_id) REFERENCES show_theme (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE shows_show_theme DROP FOREIGN KEY FK_44B0159D60669568');
        $this->addSql('DROP TABLE show_theme');
        $this->addSql('DROP TABLE shows_show_theme');
    }
}
