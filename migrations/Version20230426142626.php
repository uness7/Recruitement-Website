<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230426142626 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE application (id INT AUTO_INCREMENT NOT NULL, job_listing_id_id INT NOT NULL, candidate_id_id INT NOT NULL, applicant_name VARCHAR(255) NOT NULL, applicant_email VARCHAR(255) NOT NULL, applicant_phone_number VARCHAR(255) NOT NULL, resume LONGBLOB NOT NULL, cover_letter LONGBLOB NOT NULL, submittd_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(255) NOT NULL, INDEX IDX_A45BDDC122741817 (job_listing_id_id), INDEX IDX_A45BDDC147A475AB (candidate_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE job_listing (id INT AUTO_INCREMENT NOT NULL, recruiter_id_id INT NOT NULL, title VARCHAR(255) NOT NULL, description LONGTEXT NOT NULL, location VARCHAR(255) NOT NULL, salary VARCHAR(255) NOT NULL, employment_type VARCHAR(255) NOT NULL, qualifications LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', expires_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_51EFFF4FA2B5DF02 (recruiter_id_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC122741817 FOREIGN KEY (job_listing_id_id) REFERENCES job_listing (id)');
        $this->addSql('ALTER TABLE application ADD CONSTRAINT FK_A45BDDC147A475AB FOREIGN KEY (candidate_id_id) REFERENCES candidate (id)');
        $this->addSql('ALTER TABLE job_listing ADD CONSTRAINT FK_51EFFF4FA2B5DF02 FOREIGN KEY (recruiter_id_id) REFERENCES recruiter (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC122741817');
        $this->addSql('ALTER TABLE application DROP FOREIGN KEY FK_A45BDDC147A475AB');
        $this->addSql('ALTER TABLE job_listing DROP FOREIGN KEY FK_51EFFF4FA2B5DF02');
        $this->addSql('DROP TABLE application');
        $this->addSql('DROP TABLE job_listing');
    }
}
