<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20140904154841 extends AbstractMigration
{
    public function up(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE LineItem ADD category_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE LineItem ADD CONSTRAINT FK_F87FC2D612469DE2 FOREIGN KEY (category_id) REFERENCES BudgetCategories (id)');
        $this->addSql('CREATE INDEX IDX_F87FC2D612469DE2 ON LineItem (category_id)');
    }

    public function down(Schema $schema)
    {
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');
        
        $this->addSql('ALTER TABLE LineItem DROP FOREIGN KEY FK_F87FC2D612469DE2');
        $this->addSql('DROP INDEX IDX_F87FC2D612469DE2 ON LineItem');
        $this->addSql('ALTER TABLE LineItem DROP category_id');
    }
}
