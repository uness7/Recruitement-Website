<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Candidate;
use App\Entity\JobListing;
use App\Entity\Recruiter;
use App\Entity\User;
use App\Form\JobListingFormType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use FontLib\Table\Type\loca;
use FontLib\Table\Type\name;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class RecruitersController extends AbstractController
{
    #[Route('/recruiter', name: 'app_recruiter', methods: ['GET', 'POST'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $recruiterEmail = $this->getUser()->getUserIdentifier();
        $recruiter = $entityManager
            ->getRepository(Recruiter::class)
            ->findOneBy([
                'email' => $recruiterEmail
            ]);

        $recruiterId = $recruiter->getId();
        $recruiterCompany = $recruiter->getCompanyName();
        $recruiterName = $recruiter->getName();
        $recruiterLastName = $recruiter->getLastName();
        $recruiterJobListings = $recruiter->getJobListings();




        return $this->render('views/recruiters.html.twig',
        [
            'recruiterName' => $recruiterName,
            'recruiterLastName' => $recruiterLastName,
            'recruiterId' => $recruiterId,
            'company' => $recruiterCompany,
            'email'=> $recruiterEmail,
            'jobListings' => $recruiterJobListings,
        ]);
    }

    // Display Every Candidate in the db
    #[Route('/recruiter/{id}/getFreeCandidates', name: 'app_recruiter_getAllFreeCandidates', methods: ['GET'])]
    public function getFreeCandidates(Recruiter $recruiter, EntityManagerInterface $entityManager): Response
    {
        $recruiterId = $recruiter->getId();
        $freeCandidates = $entityManager
            ->getRepository(Candidate::class)
            ->findAll();
        return $this->render('views/recruiterFeeCandidates.html.twig',
            [
                'freeCandidates' => $freeCandidates,
                'recruiterId' => $recruiterId
            ]);
    }


    // Get the candidates associated with the recruiter
    #[Route('/recruiter/{id}/getMyCandidates/', name: 'app_recruiter_getAllCandidates', methods: ['GET'])]
    public function getCandidates(Recruiter $recruiter, EntityManagerInterface $entityManager): Response
    {
        $recruiterId = $recruiter->getId();
        $candidates = $entityManager
            ->getRepository(Candidate::class)
            ->findBy(
                [
                    'recruiter' => $recruiter
                ]
            );

        return $this->render('views/recruiterMyCandidates.html.twig',
            [
                'candidates' => $candidates,
                'recruiterId' => $recruiterId

            ]);
    }


    #[Route('/recruiter/{id}/getFreeCandidates/addCandidate/{candidateId}', name: 'app_recruiters_addcandidate', methods: ['POST', 'GET'])]
    public function addCandidateToRecruiter(
        Recruiter              $recruiter,
        EntityManagerInterface $entityManager,
        int                    $candidateId
    ): JsonResponse
    {

        $candidate = $entityManager
            ->getRepository(Candidate::class)
            ->findOneBy(['id' => $candidateId]);

        $recruiter->addCandidate($candidate);
        $entityManager->persist($recruiter);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Candidate added successfully']);
    }


    #[Route('/recruiter/{id}/getFreeCandidates/removeCandidate/{candidateId}', name: 'app_recruiters_remove_candidate', methods: ['POST', 'GET'])]
    public function removeCandidateFromRecruiter(
        Recruiter              $recruiter,
        EntityManagerInterface $entityManager,
        int                    $candidateId
    ): JsonResponse
    {
        $candidate = $entityManager
            ->getRepository(Candidate::class)
            ->findOneBy(['id' => $candidateId]);

        $recruiter->removeCandidate($candidate);
        $entityManager->persist($recruiter);
        $entityManager->flush();
        return new JsonResponse(['message' => 'Candidate removed successfully']);
    }







    #[Route('/recruiter/{id}/getMyCandidates/{candidateId}/resume', name: 'app_recruiters_displayresume')]
    public function displayResume(
        Recruiter              $recruiter,
        int                    $candidateId,
        EntityManagerInterface $entityManager
    ): Response
    {

        $candidate = $entityManager
            ->getRepository(Candidate::class)
            ->findOneBy(['id' => $candidateId]);
        $content = stream_get_contents($candidate->getResume());

        if ($content == null) {
            return new Response('Sorry, but this candidate haven\'t uploaded their cv' );
        }

        return new Response($content, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $candidate->getFirstName() . '_resume.pdf"',
        ]);



    }








    #[Route('/recruiter/update-profile', name: 'app_recruiters_update_profile', methods: ['GET', 'POST'])]
    public function updateProfile(EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        // recruiter id
        $recruiterEmail = $this->getUser()->getUserIdentifier();
        $recruiter = $entityManager
            ->getRepository(Recruiter::class)
            ->findOneBy(['email' => $recruiterEmail]);
        $user = $entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $recruiterEmail]);

        // get data
        if( $_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['email'];
            $phoneNumber = $_POST['phone'];
            $companyName = $_POST['company-name'];

            if (!$email || !$phoneNumber || !$companyName) {
                // One or more required fields are missing
                $session->getFlashBag()->add('failure', 'Sorry, but all fields are required.');
                return $this->redirectToRoute('app_recruiters_updateprofile');
            }
            // persisting updated date to the database
            $recruiter->setCompanyName($companyName);
            $recruiter->setEmail($email);
            $recruiter->setPhoneNumber($phoneNumber);

            $user->setEmail($email);

            // persist data and flush
            $entityManager->persist($recruiter);
            $entityManager->persist($user);
            $entityManager->flush();
            // display a lil message to the end user
            $session->getFlashBag()->add('success', 'Your profile\'s info has been updated.');
        }
        return $this->render('views/recruiter-update-profile.html.twig');
    }




    // Create new job posts
    #[Route('/recruiter/createJobListing', name: 'app_recruiters_create_job_listing', methods: ['POST', 'GET'])]
    public function createJobListing(
        Request                 $request,
        EntityManagerInterface $entityManager
    ) : Response {
        $recruiterEmail = $this->getUser()->getUserIdentifier();
        $recruiter = $entityManager
            ->getRepository(Recruiter::class)
            ->findOneBy(['email' => $recruiterEmail]);



        $jobListing = new JobListing();
        $jobListingForm = $this->createForm(JobListingFormType::class);
        $jobListingForm->handleRequest($request);

        if($jobListingForm->isSubmitted() && $jobListingForm->isValid()) {

            $title = $jobListingForm->get('title')->getData();
            $description = $jobListingForm->get('description')->getData();
            $location = $jobListingForm->get('location')->getData();
            $salary = $jobListingForm->get('salary')->getData();
            $employmentType = $jobListingForm->get('employmentType')->getData();
            $qualifications = $jobListingForm->get('qualifications')->getData();

            $createdAt = new DateTimeImmutable();
            $expiresAt = $createdAt->modify('+2 weeks');



            $jobListing->setTitle($title);
            $jobListing->setDescription($description);
            $jobListing->setLocation($location);
            $jobListing->setSalary($salary);
            $jobListing->setQualifications($qualifications);
            $jobListing->setEmploymentType($employmentType);
            $jobListing->setCreatedAt($createdAt);
            $jobListing->setExpiresAt($expiresAt);
            $jobListing->setRecruiterId($recruiter);


            $entityManager->persist($jobListing);
            $entityManager->flush();


            return $this->redirectToRoute(
                'app_recruiter'
            );
        }

        return $this->render(
            'views/recruiter_create_jobListing.html.twig',
            [
                'jobListingForm' => $jobListingForm->createView(),
            ]
        );
    }



    // Display Applications for a chosen post
    #[Route('/recruiter/displayApplications/{jobListingId}', name: 'app_recruiters_display_applications', methods: ['GET'])]
    public function displayApplications(
        $jobListingId,
        EntityManagerInterface $entityManager,
    ) : Response
    {
        $ourJobListing = $entityManager
            ->getRepository(JobListing::class)
            ->findOneBy(['id' => $jobListingId])
        ;

        if (!$ourJobListing) {
            throw new NotFoundHttpException('The requested job listing was not found.');
        }

        $applications = $ourJobListing->getApplications();
        $apps = $applications->getValues();

        return $this->render(
            'views/applications.html.twig',
            [
                'jobListingId' => $jobListingId,
                'applications' => $apps,
            ]
        );
    }


    #[Route(
        '/recruiter/{jobListingId}/edit-post',
        name: 'app_recruiters_edit_post',
        methods: ['GET', 'POST']
    )]
    public function editPost(
        $jobListingId,
        EntityManagerInterface $entityManager,
        Request $request,

    ) : Response
    {
        $jobListing = $entityManager->getRepository(JobListing::class)->find($jobListingId);
        $jobListingForm = $this->createForm(JobListingFormType::class, $jobListing);
        $jobListingForm->handleRequest($request);

        if($jobListingForm->isSubmitted() && $jobListingForm->isValid()) {

            $title = $jobListingForm->get('title')->getData();
            $description = $jobListingForm->get('description')->getData();
            $location = $jobListingForm->get('location')->getData();
            $salary = $jobListingForm->get('salary')->getData();
            $employmentType = $jobListingForm->get('employmentType')->getData();
            $qualifications = $jobListingForm->get('qualifications')->getData();


            if(!$title)
                $jobListing->setTitle($title);
            if(!$description)
                $jobListing->setDescription($description);
            if(!$location)
                $jobListing->setLocation($location);
            if(!$salary)
                $jobListing->setSalary($salary);
            if(!$qualifications)
                $jobListing->setQualifications($qualifications);
            if(!$employmentType)
                $jobListing->setEmploymentType($employmentType);

            $entityManager->flush();

            return $this->redirectToRoute(
                'app_recruiter',
            );
        }
        return $this->render(
            'views/recruiter_edit_post.html.twig',
            [
                'jobListingId' => $jobListingId,
                'jobListingForm' => $jobListingForm
            ]
        );
    }


    #[Route('/recruiter/displayApplications/{jobListingId}/view-resume/{applicationId}',
        name: 'app_recruiters_display_resume',
        methods: ['GET'])]
    public function displayPDF($jobListingId, $applicationId, EntityManagerInterface $entityManager): StreamedResponse
    {
        $app = $entityManager
            ->getRepository(Application::class)
            ->findOneBy(
                [
                    'id' => $applicationId
                ]
            );

        $response = new StreamedResponse();
        $response->setCallback(function () use ($app) {
            fpassthru($app->getResume());
        });
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename= resume.pdf');

        return $response;
    }

    #[Route('/recruiter/displayApplications/{jobListingId}/viewCoverLetter/{applicationId}',
        name: 'app_recruiters_display_coverLetter',
        methods: ['GET'])]
    public function displayCoverLetter($jobListingId, EntityManagerInterface $entityManager, $applicationId)
    {
        $app = $entityManager
            ->getRepository(Application::class)
            ->findOneBy(
                [
                    'id' => $applicationId
                ]
            );

        $response = new StreamedResponse();
        $response->setCallback(function () use ($app) {
            fpassthru($app->getCoverLetter());
        });
        $response->setStatusCode(200);
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename= coveletter.pdf');

        return $response;
    }
}
