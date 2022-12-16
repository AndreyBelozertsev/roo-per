<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200113105712 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE EXTENSION pg_trgm');
        $this->addSql('CREATE INDEX search_trgm_index_title_uk ON article USING gin (title_uk gin_trgm_ops)');
        $this->addSql('CREATE INDEX search_trgm_index_title_ru ON article USING gin (title_ru gin_trgm_ops)');
        $this->addSql('CREATE INDEX search_trgm_index_content_uk ON article USING gin (content_uk gin_trgm_ops)');
        $this->addSql('CREATE INDEX search_trgm_index_content_ru ON article USING gin (content_ru gin_trgm_ops)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX search_trgm_index_content_ru');
        $this->addSql('DROP INDEX search_trgm_index_content_uk');
        $this->addSql('DROP INDEX search_trgm_index_title_ru');
        $this->addSql('DROP INDEX search_trgm_index_title_uk');
    }
}
