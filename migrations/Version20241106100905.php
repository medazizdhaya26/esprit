<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241106100905 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user ADD nom VARCHAR(100) NOT NULL, ADD prenom VARCHAR(100) NOT NULL, ADD birthday DATE DEFAULT NULL, ADD telephone VARCHAR(20) DEFAULT NULL, CHANGE discr Discriminator VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE enseignant_club ADD CONSTRAINT FK_AE72A3C8E455FCC0 FOREIGN KEY (enseignant_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE etudiant_club ADD CONSTRAINT FK_E8CF9F3EDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enseignant_club DROP FOREIGN KEY FK_AE72A3C8E455FCC0');
        $this->addSql('ALTER TABLE etudiant_club DROP FOREIGN KEY FK_E8CF9F3EDDEAB1A3');
        $this->addSql('ALTER TABLE user DROP nom, DROP prenom, DROP birthday, DROP telephone, CHANGE Discriminator discr VARCHAR(255) NOT NULL');
    }
}
