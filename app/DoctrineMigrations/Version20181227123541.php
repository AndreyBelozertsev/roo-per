<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20181227123541 extends AbstractMigration
{
    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('INSERT INTO photo_report (id, title, is_published, is_deleted, views_counter, is_search_indexed) VALUES (1, \'default\', TRUE, FALSE, 0, FALSE)');
        $this->addSql('INSERT INTO feedback_form (title, slug, sort_option, visible_option, is_published, is_registered_user, is_agree_personal_data) VALUES (\'default\', \'\', \'["message","author","email"]\', \'\', TRUE, FALSE, TRUE)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM feedback_form WHERE id = 1');
        $this->addSql('DELETE FROM photo_report WHERE id = 1');
    }
}
