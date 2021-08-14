<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210814103430 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE user_watching_history (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, series_id INT NOT NULL, episode_id INT NOT NULL, date DATETIME NOT NULL, INDEX IDX_CC35729BA76ED395 (user_id), INDEX IDX_CC35729B5278319C (series_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_watching_history ADD CONSTRAINT FK_CC35729BA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE user_watching_history ADD CONSTRAINT FK_CC35729B5278319C FOREIGN KEY (series_id) REFERENCES shows (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE user_watching_history');
    }
}
