<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221219160807 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE article_category_icon_attachment (id INT NOT NULL, article_category_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE article_category_thumbnail_attachment (id INT NOT NULL, article_category_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_KJRDKPXSNFM4UCKX ON article_category_icon_attachment (article_category_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_FHCIE4TMFPKOXGR7 ON article_category_thumbnail_attachment (article_category_id)');
        $this->addSql('ALTER TABLE article_category_icon_attachment ADD CONSTRAINT FK_KJRDKPXSNFM4UCKX FOREIGN KEY (article_category_id) REFERENCES article_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article_category_icon_attachment ADD CONSTRAINT FK_FHCIE4TMFPKOXGR7 FOREIGN KEY (id) REFERENCES Attachment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article_category_thumbnail_attachment ADD CONSTRAINT FK_KJRDKPXSNFM4UCKX FOREIGN KEY (article_category_id) REFERENCES article_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article_category_thumbnail_attachment ADD CONSTRAINT FK_FHCIE4TMFPKOXGR7 FOREIGN KEY (id) REFERENCES Attachment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE article_category ADD sort INT DEFAULT 500');
        $this->addSql('ALTER TABLE article_category ADD show_in_menu BOOLEAN DEFAULT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE article_category_icon_attachment DROP CONSTRAINT FK_KJRDKPXSNFM4UCKX');
        $this->addSql('ALTER TABLE article_category_thumbnail_attachment DROP CONSTRAINT FK_FHCIE4TMFPKOXGR7');
        $this->addSql('DROP TABLE article_category_icon_attachment');
        $this->addSql('DROP TABLE article_category_thumbnail_attachment');
        $this->addSql('ALTER TABLE article_category DROP sort');
        $this->addSql('ALTER TABLE article_category DROP show_in_menu');
    }
}
