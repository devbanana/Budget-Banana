<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140909090432 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE LineItem ADD transferAccount_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE LineItem ADD CONSTRAINT FK_F87FC2D6342CC41C FOREIGN KEY (transferAccount_id) REFERENCES Account (id)');
        $this->addSql('CREATE INDEX IDX_F87FC2D6342CC41C ON LineItem (transferAccount_id)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE LineItem DROP FOREIGN KEY FK_F87FC2D6342CC41C');
        $this->addSql('DROP INDEX IDX_F87FC2D6342CC41C ON LineItem');
        $this->addSql('ALTER TABLE LineItem DROP transferAccount_id');
    }
}
