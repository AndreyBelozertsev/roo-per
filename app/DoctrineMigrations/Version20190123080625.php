<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190123080625 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE article_category (id SERIAL NOT NULL, title_uk VARCHAR(255) NOT NULL, title_ru VARCHAR(255) NOT NULL, title_en VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_53A4EDAA7C57C771 ON article_category (title_uk)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_53A4EDAAC9196CD5 ON article_category (title_ru)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_53A4EDAA46FF21AF ON article_category (title_en)');
        $this->addSql('ALTER TABLE article ALTER category_id DROP NOT NULL');
        $this->addSql('ALTER TABLE article ADD CONSTRAINT FK_23A0E6612469DE2 FOREIGN KEY (category_id) REFERENCES article_category (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('CREATE INDEX IDX_23A0E6612469DE2 ON article (category_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE article DROP CONSTRAINT FK_23A0E6612469DE2');
        $this->addSql('DROP TABLE article_category');
        $this->addSql('DROP INDEX IDX_23A0E6612469DE2');
        $this->addSql('ALTER TABLE article ALTER category_id SET NOT NULL');
    }
}
