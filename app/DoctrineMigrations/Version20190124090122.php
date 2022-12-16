<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190124090122 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE social_network_attachment (id INT NOT NULL, social_network_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_F3581427FA413953 ON social_network_attachment (social_network_id)');
        $this->addSql('CREATE TABLE social_network (id SERIAL NOT NULL, name VARCHAR(255) NOT NULL, link VARCHAR(255) NOT NULL, is_published BOOLEAN NOT NULL, is_deleted BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE social_network_attachment ADD CONSTRAINT FK_F3581427FA413953 FOREIGN KEY (social_network_id) REFERENCES social_network (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE social_network_attachment ADD CONSTRAINT FK_F3581427BF396750 FOREIGN KEY (id) REFERENCES Attachment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE social_network_attachment DROP CONSTRAINT FK_F3581427FA413953');
        $this->addSql('DROP TABLE social_network_attachment');
        $this->addSql('DROP TABLE social_network');
    }
}
