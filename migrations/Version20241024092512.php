<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241024092512 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE bibliotheque (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(30) NOT NULL, idresponsable INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE celule_ecoute (num_consultation INT AUTO_INCREMENT NOT NULL, nom_medcin VARCHAR(30) NOT NULL, nom_patient VARCHAR(30) NOT NULL, prenom_patient VARCHAR(30) NOT NULL, PRIMARY KEY(num_consultation)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE club (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(30) NOT NULL, type VARCHAR(30) NOT NULL, president VARCHAR(30) NOT NULL, supervisor VARCHAR(30) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE enseignant (id INT AUTO_INCREMENT NOT NULL, bibliotheque_id INT DEFAULT NULL, salle_de_sport_id INT DEFAULT NULL, restaurant_id INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(30) NOT NULL, date_de_naissance DATE NOT NULL, sexe VARCHAR(10) NOT NULL, email VARCHAR(70) NOT NULL, telephone VARCHAR(15) NOT NULL, matiere VARCHAR(20) NOT NULL, salaire NUMERIC(10, 2) NOT NULL, role VARCHAR(20) NOT NULL, INDEX IDX_81A72FA14419DE7D (bibliotheque_id), INDEX IDX_81A72FA1264AE1D7 (salle_de_sport_id), INDEX IDX_81A72FA1B1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE enseignant_club (enseignant_id INT NOT NULL, club_id INT NOT NULL, INDEX IDX_AE72A3C8E455FCC0 (enseignant_id), INDEX IDX_AE72A3C861190A32 (club_id), PRIMARY KEY(enseignant_id, club_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etudiant (id INT AUTO_INCREMENT NOT NULL, bibliotheque_id INT DEFAULT NULL, salle_de_sport_id INT DEFAULT NULL, restaurant_id INT DEFAULT NULL, nom VARCHAR(30) NOT NULL, prenom VARCHAR(30) NOT NULL, address VARCHAR(100) NOT NULL, date_de_naissance DATE NOT NULL, sexe VARCHAR(10) NOT NULL, email VARCHAR(70) NOT NULL, telephone VARCHAR(15) NOT NULL, filiere VARCHAR(20) NOT NULL, payement TINYINT(1) DEFAULT NULL, role VARCHAR(255) NOT NULL, is_validate TINYINT(1) NOT NULL, INDEX IDX_717E22E34419DE7D (bibliotheque_id), INDEX IDX_717E22E3264AE1D7 (salle_de_sport_id), INDEX IDX_717E22E3B1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE etudiant_club (etudiant_id INT NOT NULL, club_id INT NOT NULL, INDEX IDX_E8CF9F3EDDEAB1A3 (etudiant_id), INDEX IDX_E8CF9F3E61190A32 (club_id), PRIMARY KEY(etudiant_id, club_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE evenement (id INT AUTO_INCREMENT NOT NULL, responsable_nom VARCHAR(30) NOT NULL, responsable_id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE livre (id INT AUTO_INCREMENT NOT NULL, bibliotheque_id INT DEFAULT NULL, titre VARCHAR(255) NOT NULL, auteur VARCHAR(255) NOT NULL, isbn VARCHAR(255) NOT NULL, INDEX IDX_AC634F994419DE7D (bibliotheque_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE personnel (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE restaurant (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE salle_de_sport (id INT AUTO_INCREMENT NOT NULL, id_univercity INT NOT NULL, coechname VARCHAR(255) NOT NULL, id_superviseur INT NOT NULL, prix_des_abonnement VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL COMMENT \'(DC2Type:json)\', password VARCHAR(255) NOT NULL, username VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', available_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', delivered_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE enseignant ADD CONSTRAINT FK_81A72FA14419DE7D FOREIGN KEY (bibliotheque_id) REFERENCES bibliotheque (id)');
        $this->addSql('ALTER TABLE enseignant ADD CONSTRAINT FK_81A72FA1264AE1D7 FOREIGN KEY (salle_de_sport_id) REFERENCES salle_de_sport (id)');
        $this->addSql('ALTER TABLE enseignant ADD CONSTRAINT FK_81A72FA1B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE enseignant_club ADD CONSTRAINT FK_AE72A3C8E455FCC0 FOREIGN KEY (enseignant_id) REFERENCES enseignant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE enseignant_club ADD CONSTRAINT FK_AE72A3C861190A32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE etudiant ADD CONSTRAINT FK_717E22E34419DE7D FOREIGN KEY (bibliotheque_id) REFERENCES bibliotheque (id)');
        $this->addSql('ALTER TABLE etudiant ADD CONSTRAINT FK_717E22E3264AE1D7 FOREIGN KEY (salle_de_sport_id) REFERENCES salle_de_sport (id)');
        $this->addSql('ALTER TABLE etudiant ADD CONSTRAINT FK_717E22E3B1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id)');
        $this->addSql('ALTER TABLE etudiant_club ADD CONSTRAINT FK_E8CF9F3EDDEAB1A3 FOREIGN KEY (etudiant_id) REFERENCES etudiant (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE etudiant_club ADD CONSTRAINT FK_E8CF9F3E61190A32 FOREIGN KEY (club_id) REFERENCES club (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE livre ADD CONSTRAINT FK_AC634F994419DE7D FOREIGN KEY (bibliotheque_id) REFERENCES bibliotheque (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE enseignant DROP FOREIGN KEY FK_81A72FA14419DE7D');
        $this->addSql('ALTER TABLE enseignant DROP FOREIGN KEY FK_81A72FA1264AE1D7');
        $this->addSql('ALTER TABLE enseignant DROP FOREIGN KEY FK_81A72FA1B1E7706E');
        $this->addSql('ALTER TABLE enseignant_club DROP FOREIGN KEY FK_AE72A3C8E455FCC0');
        $this->addSql('ALTER TABLE enseignant_club DROP FOREIGN KEY FK_AE72A3C861190A32');
        $this->addSql('ALTER TABLE etudiant DROP FOREIGN KEY FK_717E22E34419DE7D');
        $this->addSql('ALTER TABLE etudiant DROP FOREIGN KEY FK_717E22E3264AE1D7');
        $this->addSql('ALTER TABLE etudiant DROP FOREIGN KEY FK_717E22E3B1E7706E');
        $this->addSql('ALTER TABLE etudiant_club DROP FOREIGN KEY FK_E8CF9F3EDDEAB1A3');
        $this->addSql('ALTER TABLE etudiant_club DROP FOREIGN KEY FK_E8CF9F3E61190A32');
        $this->addSql('ALTER TABLE livre DROP FOREIGN KEY FK_AC634F994419DE7D');
        $this->addSql('DROP TABLE bibliotheque');
        $this->addSql('DROP TABLE celule_ecoute');
        $this->addSql('DROP TABLE club');
        $this->addSql('DROP TABLE enseignant');
        $this->addSql('DROP TABLE enseignant_club');
        $this->addSql('DROP TABLE etudiant');
        $this->addSql('DROP TABLE etudiant_club');
        $this->addSql('DROP TABLE evenement');
        $this->addSql('DROP TABLE livre');
        $this->addSql('DROP TABLE personnel');
        $this->addSql('DROP TABLE restaurant');
        $this->addSql('DROP TABLE salle_de_sport');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}
