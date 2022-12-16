<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190118112002 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE post_attachment (id INT NOT NULL, post_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_5A27D07A4B89032C ON post_attachment (post_id)');
        $this->addSql('CREATE TABLE post (id SERIAL NOT NULL, title VARCHAR(1000) NOT NULL, user_name VARCHAR(255) DEFAULT NULL, user_position VARCHAR(255) DEFAULT NULL, content TEXT NOT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_published BOOLEAN NOT NULL, is_deleted BOOLEAN NOT NULL, views_counter INT NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE post_attachment ADD CONSTRAINT FK_5A27D07A4B89032C FOREIGN KEY (post_id) REFERENCES post (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE post_attachment ADD CONSTRAINT FK_5A27D07ABF396750 FOREIGN KEY (id) REFERENCES Attachment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE post_attachment DROP CONSTRAINT FK_5A27D07A4B89032C');
        $this->addSql('DROP TABLE post_attachment');
        $this->addSql('DROP TABLE post');
    }
}
