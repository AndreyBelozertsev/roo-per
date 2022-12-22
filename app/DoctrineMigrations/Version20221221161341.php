<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221221161341 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE photo_report_attachment RENAME COLUMN file_order TO sort');
        $this->addSql('ALTER TABLE photo_report_attachment ALTER sort SET DEFAULT 500');
    }

    public function down(Schema $schema) : void
    {
        $this->addSql('ALTER TABLE photo_report_attachment RENAME COLUMN sort TO file_order');
        $this->addSql('ALTER TABLE photo_report_attachment ALTER sort SET DEFAULT 0');
    }
}
