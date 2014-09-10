<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140909231028 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE AccountCategory (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, budgeted TINYINT(1) NOT NULL, sortOrder INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Account ADD budgeted TINYINT(1) NOT NULL, ADD accountCategory_id INT DEFAULT NULL, DROP subtype');
        $this->addSql('ALTER TABLE Account ADD CONSTRAINT FK_B28B6F38B0A10E9 FOREIGN KEY (accountCategory_id) REFERENCES AccountCategory (id)');
        $this->addSql('CREATE INDEX IDX_B28B6F38B0A10E9 ON Account (accountCategory_id)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE Account DROP FOREIGN KEY FK_B28B6F38B0A10E9');
        $this->addSql('DROP TABLE AccountCategory');
        $this->addSql('DROP INDEX IDX_B28B6F38B0A10E9 ON Account');
        $this->addSql('ALTER TABLE Account ADD subtype VARCHAR(255) NOT NULL, DROP budgeted, DROP accountCategory_id');
    }
}
