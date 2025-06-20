<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Migration manuelle pour mettre à jour les relations entre User et Review
 */
final class Version20250620001000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Mise à jour des relations User-Review avec reviewer et reviewee';
    }

    public function up(Schema $schema): void
    {
        // Renommer la colonne user_id en reviewer_id et ajouter reviewee_id
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');
        $this->addSql('DROP INDEX IDX_794381C6A76ED395 ON review');
        $this->addSql('ALTER TABLE review CHANGE user_id reviewer_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE review ADD reviewee_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C670574616 FOREIGN KEY (reviewer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6BD992930 FOREIGN KEY (reviewee_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_794381C670574616 ON review (reviewer_id)');
        $this->addSql('CREATE INDEX IDX_794381C6BD992930 ON review (reviewee_id)');
    }

    public function down(Schema $schema): void
    {
        // Revenir à la structure précédente
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C670574616');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6BD992930');
        $this->addSql('DROP INDEX IDX_794381C670574616 ON review');
        $this->addSql('DROP INDEX IDX_794381C6BD992930 ON review');
        $this->addSql('ALTER TABLE review CHANGE reviewer_id user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE review DROP reviewee_id');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_794381C6A76ED395 ON review (user_id)');
    }
}
