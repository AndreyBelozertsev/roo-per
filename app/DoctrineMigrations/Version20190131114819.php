<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190131114819 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE photo_report ADD description_ru TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE photo_report ADD description_en TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE photo_report RENAME COLUMN description TO description_uk');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE photo_report ADD description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE photo_report DROP description_uk');
        $this->addSql('ALTER TABLE photo_report DROP description_ru');
        $this->addSql('ALTER TABLE photo_report DROP description_en');
    }
}
