<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220522175255 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('INSERT INTO param(name, title, value) VALUES(\'editor\', \'Редактор\', \'Главный редактор - \')');
        $this->addSql('INSERT INTO param(name, title, value) VALUES(\'description\', \'Описание\', \'Описание\')');
        $this->addSql('INSERT INTO param(name, title, value) VALUES(\'age\', \'Возраст\', \'16+\')');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM param WHERE name LIKE \'editor\'');
        $this->addSql('DELETE FROM param WHERE name LIKE \'description\'');
        $this->addSql('DELETE FROM param WHERE name LIKE \'age\'');
    }
}
