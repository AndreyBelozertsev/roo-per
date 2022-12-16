<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190211113421 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('INSERT INTO param(name, title, value) VALUES(\'address\', \'Адрес\', \'г. Симферополь, ул. Название 00\')');
        $this->addSql('INSERT INTO param(name, title, value) VALUES(\'email\', \'email\', \'ukr@obshina.ru\')');
        $this->addSql('INSERT INTO param(name, title, value) VALUES(\'phone\', \'Телефон\', \'+7 978 000 00 00\')');

    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DELETE FROM param WHERE name LIKE \'phone\'');
        $this->addSql('DELETE FROM param WHERE name LIKE \'email\'');
        $this->addSql('DELETE FROM param WHERE name LIKE \'address\'');
    }
}
