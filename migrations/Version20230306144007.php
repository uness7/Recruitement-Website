<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230306144007 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE candidate (id INT AUTO_INCREMENT NOT NULL, recruiter_id INT DEFAULT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone_number VARCHAR(255) NOT NULL, resume VARCHAR(255) NOT NULL, INDEX IDX_C8B28E44156BE243 (recruiter_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recruiter (id INT AUTO_INCREMENT NOT NULL, company_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone_number VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE candidate ADD CONSTRAINT FK_C8B28E44156BE243 FOREIGN KEY (recruiter_id) REFERENCES recruiter (id)');
        $this->addSql('ALTER TABLE user ADD candidate_id INT NOT NULL, ADD recruiter_id INT NOT NULL');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D64991BD8781 FOREIGN KEY (candidate_id) REFERENCES candidate (id)');
        $this->addSql('ALTER TABLE user ADD CONSTRAINT FK_8D93D649156BE243 FOREIGN KEY (recruiter_id) REFERENCES recruiter (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D64991BD8781 ON user (candidate_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_8D93D649156BE243 ON user (recruiter_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D64991BD8781');
        $this->addSql('ALTER TABLE user DROP FOREIGN KEY FK_8D93D649156BE243');
        $this->addSql('ALTER TABLE candidate DROP FOREIGN KEY FK_C8B28E44156BE243');
        $this->addSql('DROP TABLE candidate');
        $this->addSql('DROP TABLE recruiter');
        $this->addSql('DROP INDEX UNIQ_8D93D64991BD8781 ON user');
        $this->addSql('DROP INDEX UNIQ_8D93D649156BE243 ON user');
        $this->addSql('ALTER TABLE user DROP candidate_id, DROP recruiter_id');
    }
}
