<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221011145706 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE candidate_validation (id INT AUTO_INCREMENT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE candidate_validation_annonces (candidate_validation_id INT NOT NULL, annonces_id INT NOT NULL, INDEX IDX_FCB400E48C51537D (candidate_validation_id), INDEX IDX_FCB400E44C2885D7 (annonces_id), PRIMARY KEY(candidate_validation_id, annonces_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE candidate_validation_annonces ADD CONSTRAINT FK_FCB400E48C51537D FOREIGN KEY (candidate_validation_id) REFERENCES candidate_validation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidate_validation_annonces ADD CONSTRAINT FK_FCB400E44C2885D7 FOREIGN KEY (annonces_id) REFERENCES annonces (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidate_validation_annonces DROP FOREIGN KEY FK_FCB400E48C51537D');
        $this->addSql('ALTER TABLE candidate_validation_annonces DROP FOREIGN KEY FK_FCB400E44C2885D7');
        $this->addSql('DROP TABLE candidate_validation');
        $this->addSql('DROP TABLE candidate_validation_annonces');
    }
}
