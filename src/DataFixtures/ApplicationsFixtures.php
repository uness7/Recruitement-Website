<?php

namespace App\DataFixtures;

use App\Entity\Application;
use App\Entity\JobListing;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;

class ApplicationsFixtures extends Fixture
{
    public function getJobListings(EntityManagerInterface $entityManager) : JobListing {
        return $entityManager
            ->getRepository(JobListing::class)
            ->find(1);
        ;
    }

    public function load(ObjectManager $manager)
    {
        $application = new Application();
        $entityManager = $this->getDoctrine()->getManager();
        $jobListing = $this->getJobListings($entityManager);

        $application->setJobListingId($jobListing);
        $application->setApplicantName("younes zioual");
        $application->setApplicantEmail('youn@gail.com');
        $application->setApplicantPhoneNumber("08934908");
        $application->setCandidateId(3);
        $application->setStatus('Unread');
        $application->setSubmittdAt(new DateTimeImmutable());
//         $application->setResume();
//         $application->setCoverLetter();

        $manager->persist($application);
        $manager->flush();
    }
}
