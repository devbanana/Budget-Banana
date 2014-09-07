<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140907002538 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE LineItem ADD assigned_month_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE LineItem ADD CONSTRAINT FK_F87FC2D67701BBED FOREIGN KEY (assigned_month_id) REFERENCES Budget (id)');
        $this->addSql('CREATE INDEX IDX_F87FC2D67701BBED ON LineItem (assigned_month_id)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE LineItem DROP FOREIGN KEY FK_F87FC2D67701BBED');
        $this->addSql('DROP INDEX IDX_F87FC2D67701BBED ON LineItem');
        $this->addSql('ALTER TABLE LineItem DROP assigned_month_id');
    }
}
