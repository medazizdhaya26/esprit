<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241218151050 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE biblio_abonement (id INT AUTO_INCREMENT NOT NULL, bibliotheque_id INT NOT NULL, email VARCHAR(255) NOT NULL, date_debut DATE NOT NULL, date_fin DATE NOT NULL, INDEX IDX_4D663D9D4419DE7D (bibliotheque_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE biblio_abonement ADD CONSTRAINT FK_4D663D9D4419DE7D FOREIGN KEY (bibliotheque_id) REFERENCES bibliotheque (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE biblio_abonement DROP FOREIGN KEY FK_4D663D9D4419DE7D');
        $this->addSql('DROP TABLE biblio_abonement');
    }
}
