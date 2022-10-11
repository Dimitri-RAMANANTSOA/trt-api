<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221011153231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE applications (id INT AUTO_INCREMENT NOT NULL, annonce_id INT DEFAULT NULL, applicant_id INT DEFAULT NULL, INDEX IDX_F7C966F08805AB2F (annonce_id), INDEX IDX_F7C966F097139001 (applicant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_F7C966F08805AB2F FOREIGN KEY (annonce_id) REFERENCES annonces (id)');
        $this->addSql('ALTER TABLE applications ADD CONSTRAINT FK_F7C966F097139001 FOREIGN KEY (applicant_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE annonces_user DROP FOREIGN KEY FK_B755A880A76ED395');
        $this->addSql('ALTER TABLE annonces_user DROP FOREIGN KEY FK_B755A8804C2885D7');
        $this->addSql('DROP TABLE annonces_user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE annonces_user (annonces_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_B755A880A76ED395 (user_id), INDEX IDX_B755A8804C2885D7 (annonces_id), PRIMARY KEY(annonces_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE annonces_user ADD CONSTRAINT FK_B755A880A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE annonces_user ADD CONSTRAINT FK_B755A8804C2885D7 FOREIGN KEY (annonces_id) REFERENCES annonces (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE applications DROP FOREIGN KEY FK_F7C966F08805AB2F');
        $this->addSql('ALTER TABLE applications DROP FOREIGN KEY FK_F7C966F097139001');
        $this->addSql('DROP TABLE applications');
    }
}
