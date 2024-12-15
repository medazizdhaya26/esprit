<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241215151032 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE chambre (id INT AUTO_INCREMENT NOT NULL, foyer_id INT NOT NULL, numero_chambre VARCHAR(50) NOT NULL, type VARCHAR(100) NOT NULL, est_disponible TINYINT(1) NOT NULL, climatiseur TINYINT(1) NOT NULL, chauffage_central TINYINT(1) NOT NULL, places_disponibles INT NOT NULL, INDEX IDX_C509E4FF2B919A58 (foyer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE foyer (id INT AUTO_INCREMENT NOT NULL, nom_foyer VARCHAR(100) NOT NULL, nombre_chambre INT DEFAULT NULL, nombre_etage INT NOT NULL, lieu_foyer VARCHAR(100) NOT NULL, nombre_chambres_single INT NOT NULL, nombre_chambres_double INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE notification (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, message VARCHAR(255) NOT NULL, is_read TINYINT(1) NOT NULL, id_type VARCHAR(255) NOT NULL, id_user VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE reserv_chambre (id INT AUTO_INCREMENT NOT NULL, chambre_id INT NOT NULL, foyer_id INT NOT NULL, email VARCHAR(255) NOT NULL, niveau_universitaire INT NOT NULL, type_chambre VARCHAR(50) NOT NULL, is_validate TINYINT(1) DEFAULT 0 NOT NULL, INDEX IDX_1E573B0D9B177F54 (chambre_id), INDEX IDX_1E573B0D2B919A58 (foyer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE chambre ADD CONSTRAINT FK_C509E4FF2B919A58 FOREIGN KEY (foyer_id) REFERENCES foyer (id)');
        $this->addSql('ALTER TABLE reserv_chambre ADD CONSTRAINT FK_1E573B0D9B177F54 FOREIGN KEY (chambre_id) REFERENCES chambre (id)');
        $this->addSql('ALTER TABLE reserv_chambre ADD CONSTRAINT FK_1E573B0D2B919A58 FOREIGN KEY (foyer_id) REFERENCES foyer (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE chambre DROP FOREIGN KEY FK_C509E4FF2B919A58');
        $this->addSql('ALTER TABLE reserv_chambre DROP FOREIGN KEY FK_1E573B0D9B177F54');
        $this->addSql('ALTER TABLE reserv_chambre DROP FOREIGN KEY FK_1E573B0D2B919A58');
        $this->addSql('DROP TABLE chambre');
        $this->addSql('DROP TABLE foyer');
        $this->addSql('DROP TABLE notification');
        $this->addSql('DROP TABLE reserv_chambre');
    }
}
