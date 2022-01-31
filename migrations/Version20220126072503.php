<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220126072503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE difficulte ADD structure_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE difficulte ADD CONSTRAINT FK_AF6A33A02534008B FOREIGN KEY (structure_id) REFERENCES structure (id)');
        $this->addSql('CREATE INDEX IDX_AF6A33A02534008B ON difficulte (structure_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE difficulte DROP FOREIGN KEY FK_AF6A33A02534008B');
        $this->addSql('DROP INDEX IDX_AF6A33A02534008B ON difficulte');
        $this->addSql('ALTER TABLE difficulte DROP structure_id');
    }
}
