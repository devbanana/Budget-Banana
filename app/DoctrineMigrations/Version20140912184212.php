<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140912184212 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE Account ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Account ADD CONSTRAINT FK_B28B6F38A76ED395 FOREIGN KEY (user_id) REFERENCES BudgetUser (id)');
        $this->addSql('CREATE INDEX IDX_B28B6F38A76ED395 ON Account (user_id)');
        $this->addSql('ALTER TABLE Payee ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Payee ADD CONSTRAINT FK_3D9F15AA76ED395 FOREIGN KEY (user_id) REFERENCES BudgetUser (id)');
        $this->addSql('CREATE INDEX IDX_3D9F15AA76ED395 ON Payee (user_id)');
        $this->addSql('ALTER TABLE Payer ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Payer ADD CONSTRAINT FK_800A749DA76ED395 FOREIGN KEY (user_id) REFERENCES BudgetUser (id)');
        $this->addSql('CREATE INDEX IDX_800A749DA76ED395 ON Payer (user_id)');
        $this->addSql('ALTER TABLE Transaction ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Transaction ADD CONSTRAINT FK_F4AB8A06A76ED395 FOREIGN KEY (user_id) REFERENCES BudgetUser (id)');
        $this->addSql('CREATE INDEX IDX_F4AB8A06A76ED395 ON Transaction (user_id)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE Account DROP FOREIGN KEY FK_B28B6F38A76ED395');
        $this->addSql('DROP INDEX IDX_B28B6F38A76ED395 ON Account');
        $this->addSql('ALTER TABLE Account DROP user_id');
        $this->addSql('ALTER TABLE Payee DROP FOREIGN KEY FK_3D9F15AA76ED395');
        $this->addSql('DROP INDEX IDX_3D9F15AA76ED395 ON Payee');
        $this->addSql('ALTER TABLE Payee DROP user_id');
        $this->addSql('ALTER TABLE Payer DROP FOREIGN KEY FK_800A749DA76ED395');
        $this->addSql('DROP INDEX IDX_800A749DA76ED395 ON Payer');
        $this->addSql('ALTER TABLE Payer DROP user_id');
        $this->addSql('ALTER TABLE Transaction DROP FOREIGN KEY FK_F4AB8A06A76ED395');
        $this->addSql('DROP INDEX IDX_F4AB8A06A76ED395 ON Transaction');
        $this->addSql('ALTER TABLE Transaction DROP user_id');
    }
}
