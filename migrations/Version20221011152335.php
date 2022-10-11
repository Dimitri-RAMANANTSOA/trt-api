<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20221011152335 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE candidate_validation_annonces DROP FOREIGN KEY FK_FCB400E48C51537D');
        $this->addSql('ALTER TABLE candidate_validation_annonces DROP FOREIGN KEY FK_FCB400E44C2885D7');
        $this->addSql('ALTER TABLE candidate_validation_user DROP FOREIGN KEY FK_8C0D4B7B8C51537D');
        $this->addSql('ALTER TABLE candidate_validation_user DROP FOREIGN KEY FK_8C0D4B7BA76ED395');
        $this->addSql('DROP TABLE candidate_validation');
        $this->addSql('DROP TABLE candidate_validation_annonces');
        $this->addSql('DROP TABLE candidate_validation_user');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE candidate_validation (id INT AUTO_INCREMENT NOT NULL, is_validated TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE candidate_validation_annonces (candidate_validation_id INT NOT NULL, annonces_id INT NOT NULL, INDEX IDX_FCB400E44C2885D7 (annonces_id), INDEX IDX_FCB400E48C51537D (candidate_validation_id), PRIMARY KEY(candidate_validation_id, annonces_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('CREATE TABLE candidate_validation_user (candidate_validation_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_8C0D4B7B8C51537D (candidate_validation_id), INDEX IDX_8C0D4B7BA76ED395 (user_id), PRIMARY KEY(candidate_validation_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE candidate_validation_annonces ADD CONSTRAINT FK_FCB400E48C51537D FOREIGN KEY (candidate_validation_id) REFERENCES candidate_validation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidate_validation_annonces ADD CONSTRAINT FK_FCB400E44C2885D7 FOREIGN KEY (annonces_id) REFERENCES annonces (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidate_validation_user ADD CONSTRAINT FK_8C0D4B7B8C51537D FOREIGN KEY (candidate_validation_id) REFERENCES candidate_validation (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE candidate_validation_user ADD CONSTRAINT FK_8C0D4B7BA76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }
}
