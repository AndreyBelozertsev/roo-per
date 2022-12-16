<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190125093658 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE attachment ADD file_description_ru TEXT DEFAULT \'\'');
        $this->addSql('ALTER TABLE attachment ADD file_description_en TEXT DEFAULT \'\'');
        $this->addSql('ALTER TABLE attachment RENAME COLUMN file_description TO file_description_uk');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE Attachment ADD file_description TEXT DEFAULT \'\'');
        $this->addSql('ALTER TABLE Attachment DROP file_description_uk');
        $this->addSql('ALTER TABLE Attachment DROP file_description_ru');
        $this->addSql('ALTER TABLE Attachment DROP file_description_en');
    }
}
