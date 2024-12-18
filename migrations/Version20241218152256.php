<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241218152256 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE biblio_abonement DROP FOREIGN KEY FK_4D663D9D4419DE7D');
        $this->addSql('DROP INDEX IDX_4D663D9D4419DE7D ON biblio_abonement');
        $this->addSql('ALTER TABLE biblio_abonement ADD id_bibliotheque VARCHAR(255) NOT NULL, DROP bibliotheque_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE biblio_abonement ADD bibliotheque_id INT NOT NULL, DROP id_bibliotheque');
        $this->addSql('ALTER TABLE biblio_abonement ADD CONSTRAINT FK_4D663D9D4419DE7D FOREIGN KEY (bibliotheque_id) REFERENCES bibliotheque (id)');
        $this->addSql('CREATE INDEX IDX_4D663D9D4419DE7D ON biblio_abonement (bibliotheque_id)');
    }
}
