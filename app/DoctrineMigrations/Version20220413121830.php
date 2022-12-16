<?php declare(strict_types=1);

namespace Application\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220413121830 extends AbstractMigration
{
    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('CREATE TABLE magazine_newspaper_attachment (id INT NOT NULL, magazine_newspaper_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE TABLE magazine_newspaper_document_attachment (id INT NOT NULL, magazine_newspaper_id INT DEFAULT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_HV6VKPXSNFMGXUHI ON magazine_newspaper_attachment (magazine_newspaper_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_JM4VMYTMFP5LPNMU ON magazine_newspaper_document_attachment (magazine_newspaper_id)');
        $this->addSql('CREATE TABLE magazine_newspaper (id SERIAL NOT NULL, title_uk VARCHAR(250) NOT NULL, title_ru VARCHAR(250) DEFAULT NULL, title_en VARCHAR(250) NOT NULL, type_of VARCHAR(20) DEFAULT \'magazine\', created_at TIMESTAMP(0) WITHOUT TIME ZONE NOT NULL, is_published BOOLEAN NOT NULL, is_deleted BOOLEAN NOT NULL, PRIMARY KEY(id))');
        $this->addSql('ALTER TABLE magazine_newspaper_attachment ADD CONSTRAINT FK_HV6VKPXSNFMGXUHI FOREIGN KEY (magazine_newspaper_id) REFERENCES magazine_newspaper (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE magazine_newspaper_attachment ADD CONSTRAINT FK_JM4VMYTMFP5LPNMU FOREIGN KEY (id) REFERENCES Attachment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE magazine_newspaper_document_attachment ADD CONSTRAINT FK_HV6VKPXSNFMGXUHI FOREIGN KEY (magazine_newspaper_id) REFERENCES magazine_newspaper (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE magazine_newspaper_document_attachment ADD CONSTRAINT FK_JM4VMYTMFP5LPNMU FOREIGN KEY (id) REFERENCES Attachment (id) ON DELETE CASCADE NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'postgresql', 'Migration can only be executed safely on \'postgresql\'.');

        $this->addSql('ALTER TABLE magazine_newspaper_attachment DROP CONSTRAINT FK_HV6VKPXSNFMGXUHI');
        $this->addSql('ALTER TABLE magazine_newspaper_document_attachment DROP CONSTRAINT FK_JM4VMYTMFP5LPNMU');
        $this->addSql('DROP TABLE magazine_newspaper_attachment');
        $this->addSql('DROP TABLE magazine_newspaper_document_attachment');
        $this->addSql('DROP TABLE magazine_newspaper');
    }
}
