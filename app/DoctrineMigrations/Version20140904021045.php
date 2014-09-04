<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140904021045 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('CREATE TABLE Budget (id INT AUTO_INCREMENT NOT NULL, month DATE NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE BudgetCategories (id INT AUTO_INCREMENT NOT NULL, budget_id INT DEFAULT NULL, category_id INT DEFAULT NULL, budgeted NUMERIC(14, 2) NOT NULL, outflow NUMERIC(14, 2) NOT NULL, balance NUMERIC(14, 2) NOT NULL, INDEX IDX_FE61377836ABA6B8 (budget_id), INDEX IDX_FE61377812469DE2 (category_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Category (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, masterCategory_id INT DEFAULT NULL, INDEX IDX_FF3A7B97FF8F40C3 (masterCategory_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE MasterCategory (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Payee (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Payer (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE BudgetCategories ADD CONSTRAINT FK_FE61377836ABA6B8 FOREIGN KEY (budget_id) REFERENCES Budget (id)');
        $this->addSql('ALTER TABLE BudgetCategories ADD CONSTRAINT FK_FE61377812469DE2 FOREIGN KEY (category_id) REFERENCES Category (id)');
        $this->addSql('ALTER TABLE Category ADD CONSTRAINT FK_FF3A7B97FF8F40C3 FOREIGN KEY (masterCategory_id) REFERENCES MasterCategory (id)');
        $this->addSql('ALTER TABLE LineItem ADD payee_id INT DEFAULT NULL, ADD payer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE LineItem ADD CONSTRAINT FK_F87FC2D6CB4B68F FOREIGN KEY (payee_id) REFERENCES Payee (id)');
        $this->addSql('ALTER TABLE LineItem ADD CONSTRAINT FK_F87FC2D6C17AD9A9 FOREIGN KEY (payer_id) REFERENCES Payer (id)');
        $this->addSql('CREATE INDEX IDX_F87FC2D6CB4B68F ON LineItem (payee_id)');
        $this->addSql('CREATE INDEX IDX_F87FC2D6C17AD9A9 ON LineItem (payer_id)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE BudgetCategories DROP FOREIGN KEY FK_FE61377836ABA6B8');
        $this->addSql('ALTER TABLE BudgetCategories DROP FOREIGN KEY FK_FE61377812469DE2');
        $this->addSql('ALTER TABLE Category DROP FOREIGN KEY FK_FF3A7B97FF8F40C3');
        $this->addSql('ALTER TABLE LineItem DROP FOREIGN KEY FK_F87FC2D6CB4B68F');
        $this->addSql('ALTER TABLE LineItem DROP FOREIGN KEY FK_F87FC2D6C17AD9A9');
        $this->addSql('DROP TABLE Budget');
        $this->addSql('DROP TABLE BudgetCategories');
        $this->addSql('DROP TABLE Category');
        $this->addSql('DROP TABLE MasterCategory');
        $this->addSql('DROP TABLE Payee');
        $this->addSql('DROP TABLE Payer');
        $this->addSql('DROP INDEX IDX_F87FC2D6CB4B68F ON LineItem');
        $this->addSql('DROP INDEX IDX_F87FC2D6C17AD9A9 ON LineItem');
        $this->addSql('ALTER TABLE LineItem DROP payee_id, DROP payer_id');
    }
}
