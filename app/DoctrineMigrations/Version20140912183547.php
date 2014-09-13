<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140912183547 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE Budget ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE Budget ADD CONSTRAINT FK_745EF24DA76ED395 FOREIGN KEY (user_id) REFERENCES BudgetUser (id)');
        $this->addSql('CREATE INDEX IDX_745EF24DA76ED395 ON Budget (user_id)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE Budget DROP FOREIGN KEY FK_745EF24DA76ED395');
        $this->addSql('DROP INDEX IDX_745EF24DA76ED395 ON Budget');
        $this->addSql('ALTER TABLE Budget DROP user_id');
    }
}
