<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221219184253 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE SEQUENCE magazine_article_id_seq INCREMENT BY 1 MINVALUE 1 START 1');
        $this->addSql('CREATE TABLE magazine_article (id INT NOT NULL, menu_node_id INT DEFAULT NULL, title_uk VARCHAR(1000) NOT NULL, title_ru VARCHAR(1000) NOT NULL, title_en VARCHAR(1000) NOT NULL, slug VARCHAR(150) NOT NULL, subtitle_uk VARCHAR(255) DEFAULT NULL, subtitle_ru VARCHAR(255) DEFAULT NULL, subtitle_en VARCHAR(255) DEFAULT NULL, content_uk TEXT DEFAULT NULL, content_ru TEXT DEFAULT NULL, content_en TEXT DEFAULT NULL, author_id INT NOT NULL, magazine_id INT DEFAULT NULL, sort INT DEFAULT 500, is_published BOOLEAN DEFAULT \'true\', published_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, views_counter INT DEFAULT 0 NOT NULL, manual_views_counter INT DEFAULT 0 NOT NULL, original_magazine_article_id INT DEFAULT NULL, old_id INT DEFAULT NULL, original_instance_code VARCHAR(50) DEFAULT NULL, update_at TIMESTAMP(0) WITHOUT TIME ZONE DEFAULT NULL, original_file_path VARCHAR(1000) DEFAULT NULL, related VARCHAR(1000) DEFAULT NULL, is_deleted BOOLEAN DEFAULT NULL, is_search_indexed BOOLEAN DEFAULT \'false\' NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_NCJEJI5KYIOL3GS2 ON magazine_article (menu_node_id)');
        $this->addSql('CREATE TABLE magazine_article_attachment (id INT NOT NULL, magazine_article_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_GDOW86NSHKCP4R9N ON magazine_article_attachment (magazine_article_id)');
        $this->addSql('CREATE TABLE magazine_article_media_attachment (id INT NOT NULL, magazine_article_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8EMD8JQFHD9J5SB7 ON magazine_article_media_attachment (magazine_article_id)');
        $this->addSql('ALTER TABLE magazine_article ADD CONSTRAINT FK_NCJEJI5KYIOL3GS2 FOREIGN KEY (menu_node_id) REFERENCES menu_node (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE magazine_article_attachment ADD CONSTRAINT FK_GDOW86NSHKCP4R9N FOREIGN KEY (magazine_article_id) REFERENCES magazine_article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE magazine_article_attachment ADD CONSTRAINT FK_7DKU7V7A37NLA8ND FOREIGN KEY (id) REFERENCES Attachment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE magazine_article_media_attachment ADD CONSTRAINT FK_8EMD8JQFHD9J5SB7 FOREIGN KEY (magazine_article_id) REFERENCES magazine_article (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE magazine_article_media_attachment ADD CONSTRAINT FK_HO8HX7AJQS7E6MA9 FOREIGN KEY (id) REFERENCES Attachment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE magazine_article ADD CONSTRAINT FK_WI79SA8KJF12PQC6 FOREIGN KEY (magazine_id) REFERENCES magazine_newspaper (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_WI79SA8KJF12PQC6 ON magazine_article (magazine_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');
        
        $this->addSql('ALTER TABLE magazine_article DROP CONSTRAINT FK_NCJEJI5KYIOL3GS2');
        $this->addSql('ALTER TABLE magazine_article_attachment DROP CONSTRAINT FK_GDOW86NSHKCP4R9N');
        $this->addSql('ALTER TABLE magazine_article_media_attachment DROP CONSTRAINT FK_8EMD8JQFHD9J5SB7');
        $this->addSql('ALTER TABLE magazine_article_attachment DROP CONSTRAINT FK_7DKU7V7A37NLA8ND');
        $this->addSql('ALTER TABLE magazine_article_media_attachment DROP CONSTRAINT FK_HO8HX7AJQS7E6MA9');
        $this->addSql('ALTER TABLE magazine_article DROP CONSTRAINT FK_23A0E66462CF2E1');
        $this->addSql('DROP SEQUENCE magazine_article_id_seq CASCADE');
        $this->addSql('DROP TABLE magazine_article');
        $this->addSql('DROP TABLE magazine_article_attachment');
        $this->addSql('DROP TABLE magazine_article_media_attachment');
        $this->addSql('DROP INDEX IDX_WI79SA8KJF12PQC6');
        
    }
}
