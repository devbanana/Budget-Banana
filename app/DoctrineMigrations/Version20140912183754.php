<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140912183754 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE MasterCategory ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE MasterCategory ADD CONSTRAINT FK_9A310F5AA76ED395 FOREIGN KEY (user_id) REFERENCES BudgetUser (id)');
        $this->addSql('CREATE INDEX IDX_9A310F5AA76ED395 ON MasterCategory (user_id)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE MasterCategory DROP FOREIGN KEY FK_9A310F5AA76ED395');
        $this->addSql('DROP INDEX IDX_9A310F5AA76ED395 ON MasterCategory');
        $this->addSql('ALTER TABLE MasterCategory DROP user_id');
    }
}
