<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190131145102 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX uniq_972a1cc62b36786b');
        $this->addSql('ALTER TABLE feedback_form ADD title_ru VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE feedback_form ADD title_en VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE feedback_form ADD description_ru TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE feedback_form ADD description_en TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE feedback_form RENAME COLUMN title TO title_uk');
        $this->addSql('ALTER TABLE feedback_form RENAME COLUMN description TO description_uk');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_972A1CC67C57C771 ON feedback_form (title_uk)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('DROP INDEX UNIQ_972A1CC67C57C771');
        $this->addSql('ALTER TABLE feedback_form ADD title VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE feedback_form ADD description TEXT DEFAULT NULL');
        $this->addSql('ALTER TABLE feedback_form DROP title_uk');
        $this->addSql('ALTER TABLE feedback_form DROP title_ru');
        $this->addSql('ALTER TABLE feedback_form DROP title_en');
        $this->addSql('ALTER TABLE feedback_form DROP description_uk');
        $this->addSql('ALTER TABLE feedback_form DROP description_ru');
        $this->addSql('ALTER TABLE feedback_form DROP description_en');
        $this->addSql('CREATE UNIQUE INDEX uniq_972a1cc62b36786b ON feedback_form (title)');
    }
}
