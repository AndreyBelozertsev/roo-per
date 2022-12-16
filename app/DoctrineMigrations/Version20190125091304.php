<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190125091304 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE post ADD title_ru VARCHAR(1000) DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD title_en VARCHAR(1000) DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD user_name_uk VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD user_name_ru VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD user_name_en VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD user_position_uk VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD user_position_ru VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD user_position_en VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD content_ru TEXT NOT NULL');
        $this->addSql('ALTER TABLE post ADD content_en TEXT NOT NULL');
        $this->addSql('ALTER TABLE post DROP user_name');
        $this->addSql('ALTER TABLE post DROP user_position');
        $this->addSql('ALTER TABLE post RENAME COLUMN title TO title_uk');
        $this->addSql('ALTER TABLE post RENAME COLUMN content TO content_uk');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE post ADD user_name VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD user_position VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE post ADD content TEXT NOT NULL');
        $this->addSql('ALTER TABLE post DROP title_ru');
        $this->addSql('ALTER TABLE post DROP title_en');
        $this->addSql('ALTER TABLE post DROP user_name_uk');
        $this->addSql('ALTER TABLE post DROP user_name_ru');
        $this->addSql('ALTER TABLE post DROP user_name_en');
        $this->addSql('ALTER TABLE post DROP user_position_uk');
        $this->addSql('ALTER TABLE post DROP user_position_ru');
        $this->addSql('ALTER TABLE post DROP user_position_en');
        $this->addSql('ALTER TABLE post DROP content_uk');
        $this->addSql('ALTER TABLE post DROP content_ru');
        $this->addSql('ALTER TABLE post DROP content_en');
        $this->addSql('ALTER TABLE post RENAME COLUMN title_uk TO title');
    }
}
