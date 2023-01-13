<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221226203156 extends AbstractMigration
{
        public function up(Schema $schema) : void
        {
            // this up() migration is auto-generated, please modify it to your needs
            $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

            $this->addSql('ALTER TABLE comment ADD magazine_article_id INT DEFAULT NULL');
            $this->addSql('ALTER TABLE comment ADD CONSTRAINT FK_HD83LE3KJSU75M7S FOREIGN KEY (magazine_article_id) REFERENCES magazine_article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
            $this->addSql('CREATE INDEX IDX_HD83LE3KJSU75M7S ON comment (magazine_article_id)');
        }
    
        public function down(Schema $schema) : void
        {
            // this down() migration is auto-generated, please modify it to your needs
            $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');
    
            $this->addSql('ALTER TABLE comment DROP CONSTRAINT FK_HD83LE3KJSU75M7S');
            $this->addSql('DROP INDEX IDX_HD83LE3KJSU75M7S');
            $this->addSql('ALTER TABLE comment DROP magazine_article_id');
        }
}
