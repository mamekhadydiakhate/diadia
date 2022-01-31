<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220106154142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement ADD start DATE DEFAULT NULL, ADD end DATE DEFAULT NULL, DROP date_debut, DROP date_fin');
        $this->addSql('ALTER TABLE periodicite ADD start DATE NOT NULL, ADD end DATE NOT NULL, DROP date_debut, DROP date_fin');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE evenement ADD date_debut DATE DEFAULT NULL, ADD date_fin DATE DEFAULT NULL, DROP start, DROP end');
        $this->addSql('ALTER TABLE periodicite ADD date_debut DATE NOT NULL, ADD date_fin DATE NOT NULL, DROP start, DROP end');
    }
}
