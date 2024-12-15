<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241215174254 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bibliothecaire (id INT AUTO_INCREMENT NOT NULL, bibliotheque_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, date_naissance DATE NOT NULL, competences LONGTEXT NOT NULL COMMENT \'(DC2Type:array)\', specialisation VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_F74495A4E7927C74 (email), INDEX IDX_F74495A44419DE7D (bibliotheque_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE bibliothecaire ADD CONSTRAINT FK_F74495A44419DE7D FOREIGN KEY (bibliotheque_id) REFERENCES bibliotheque (id)');
        $this->addSql('ALTER TABLE bibliotheque ADD horaires_ouverture VARCHAR(255) NOT NULL, ADD contact_administratif VARCHAR(255) NOT NULL, ADD adresse VARCHAR(255) DEFAULT NULL, ADD description LONGTEXT DEFAULT NULL, CHANGE nom nom VARCHAR(255) NOT NULL, CHANGE idresponsable capacite INT NOT NULL');
        $this->addSql('ALTER TABLE livre DROP FOREIGN KEY FK_AC634F994419DE7D');
        $this->addSql('DROP INDEX IDX_AC634F994419DE7D ON livre');
        $this->addSql('ALTER TABLE livre ADD description LONGTEXT DEFAULT NULL, ADD date_publication DATE DEFAULT NULL, ADD categorie VARCHAR(255) DEFAULT NULL, ADD stock INT NOT NULL, ADD langue VARCHAR(50) DEFAULT NULL, CHANGE bibliotheque_id nombre_pages INT DEFAULT NULL, CHANGE isbn unique_id VARCHAR(255) NOT NULL');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_AC634F99E3C68343 ON livre (unique_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE bibliothecaire DROP FOREIGN KEY FK_F74495A44419DE7D');
        $this->addSql('DROP TABLE bibliothecaire');
        $this->addSql('ALTER TABLE bibliotheque DROP horaires_ouverture, DROP contact_administratif, DROP adresse, DROP description, CHANGE nom nom VARCHAR(30) NOT NULL, CHANGE capacite idresponsable INT NOT NULL');
        $this->addSql('DROP INDEX UNIQ_AC634F99E3C68343 ON livre');
        $this->addSql('ALTER TABLE livre DROP description, DROP date_publication, DROP categorie, DROP stock, DROP langue, CHANGE nombre_pages bibliotheque_id INT DEFAULT NULL, CHANGE unique_id isbn VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE livre ADD CONSTRAINT FK_AC634F994419DE7D FOREIGN KEY (bibliotheque_id) REFERENCES bibliotheque (id)');
        $this->addSql('CREATE INDEX IDX_AC634F994419DE7D ON livre (bibliotheque_id)');
    }
}
