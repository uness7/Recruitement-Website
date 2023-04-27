<?php

namespace App\Entity;

use App\Repository\ApplicationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApplicationRepository::class)]
class Application
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $applicantName = null;

    #[ORM\Column(length: 255)]
    private ?string $applicantEmail = null;

    #[ORM\Column(length: 255)]
    private ?string $applicantPhoneNumber = null;

    #[ORM\Column(type: Types::BLOB)]
    private $resume = null;

    #[ORM\Column(type: Types::BLOB)]
    private $coverLetter = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $submittdAt = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\ManyToOne(inversedBy: 'applications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?JobListing $jobListingId = null;

    #[ORM\ManyToOne(inversedBy: 'applications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Candidate $candidateId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getApplicantName(): ?string
    {
        return $this->applicantName;
    }

    public function setApplicantName(string $applicantName): self
    {
        $this->applicantName = $applicantName;

        return $this;
    }

    public function getApplicantEmail(): ?string
    {
        return $this->applicantEmail;
    }

    public function setApplicantEmail(string $applicantEmail): self
    {
        $this->applicantEmail = $applicantEmail;

        return $this;
    }

    public function getApplicantPhoneNumber(): ?string
    {
        return $this->applicantPhoneNumber;
    }

    public function setApplicantPhoneNumber(string $applicantPhoneNumber): self
    {
        $this->applicantPhoneNumber = $applicantPhoneNumber;

        return $this;
    }

    public function getResume()
    {
        return $this->resume;
    }

    public function setResume($resume): self
    {
        $this->resume = $resume;

        return $this;
    }

    public function getCoverLetter()
    {
        return $this->coverLetter;
    }

    public function setCoverLetter($coverLetter): self
    {
        $this->coverLetter = $coverLetter;

        return $this;
    }

    public function getSubmittdAt(): ?\DateTimeImmutable
    {
        return $this->submittdAt;
    }

    public function setSubmittdAt(\DateTimeImmutable $submittdAt): self
    {
        $this->submittdAt = $submittdAt;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getJobListingId(): ?JobListing
    {
        return $this->jobListingId;
    }

    public function setJobListingId(?JobListing $jobListingId): self
    {
        $this->jobListingId = $jobListingId;

        return $this;
    }

    public function getCandidateId(): ?Candidate
    {
        return $this->candidateId;
    }

    public function setCandidateId(?Candidate $candidateId): self
    {
        $this->candidateId = $candidateId;

        return $this;
    }
}
