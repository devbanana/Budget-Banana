<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140902213741 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE Subtransaction (id INT AUTO_INCREMENT NOT NULL, transaction_id INT DEFAULT NULL, account_id INT DEFAULT NULL, type VARCHAR(255) NOT NULL, inflow NUMERIC(10, 0) NOT NULL, outflow NUMERIC(10, 0) NOT NULL, INDEX IDX_F0D31EBC2FC0CB0F (transaction_id), INDEX IDX_F0D31EBC9B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Transaction (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, inflow NUMERIC(14, 2) NOT NULL, outflow NUMERIC(14, 2) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Subtransaction ADD CONSTRAINT FK_F0D31EBC2FC0CB0F FOREIGN KEY (transaction_id) REFERENCES Transaction (id)');
        $this->addSql('ALTER TABLE Subtransaction ADD CONSTRAINT FK_F0D31EBC9B6B5FBA FOREIGN KEY (account_id) REFERENCES Account (id)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE Subtransaction DROP FOREIGN KEY FK_F0D31EBC2FC0CB0F');
        $this->addSql('DROP TABLE Subtransaction');
        $this->addSql('DROP TABLE Transaction');
    }
}
