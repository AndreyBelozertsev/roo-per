<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190128085803 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE article ADD title_ru VARCHAR(1000) NOT NULL');
        $this->addSql('ALTER TABLE article ADD title_en VARCHAR(1000) NOT NULL');
        $this->addSql('ALTER TABLE article ADD subtitle_ru VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD subtitle_en VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD content_ru TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD content_en TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE article RENAME COLUMN title TO title_uk');
        $this->addSql('ALTER TABLE article RENAME COLUMN subtitle TO subtitle_uk');
        $this->addSql('ALTER TABLE article RENAME COLUMN content TO content_uk');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE article ADD title VARCHAR(1000) NOT NULL');
        $this->addSql('ALTER TABLE article ADD subtitle VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE article ADD content TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE article DROP title_uk');
        $this->addSql('ALTER TABLE article DROP title_ru');
        $this->addSql('ALTER TABLE article DROP title_en');
        $this->addSql('ALTER TABLE article DROP subtitle_uk');
        $this->addSql('ALTER TABLE article DROP subtitle_ru');
        $this->addSql('ALTER TABLE article DROP subtitle_en');
        $this->addSql('ALTER TABLE article DROP content_uk');
        $this->addSql('ALTER TABLE article DROP content_ru');
        $this->addSql('ALTER TABLE article DROP content_en');
    }
}
