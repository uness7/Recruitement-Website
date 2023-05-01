<?php

namespace App\Controller;

use App\Controller\Queries\HomepageSearchData;
use App\Entity\Application;
use App\Entity\Candidate;
use App\Entity\JobListing;
use App\Form\ApplicationFormType;
use App\Form\JobsSearchFromType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{

    #[Route('/', name: 'app_homepage', methods: ['GET', 'POST'])]
    public function index(
        EntityManagerInterface $entityManager,
        Request $request,
    ): Response
    {

        $searchForm = $this->createForm(JobsSearchFromType::class);
        $searchForm->handleRequest($request);


        if ($searchForm->isSubmitted() && $searchForm->isValid()) {
            $input = $searchForm->get('title')->getData();
            $searchData = new HomepageSearchData(
                $input,
            );

            $jobListings = $entityManager
                ->getRepository(JobListing::class)
                ->searchJobListings($searchData);
            ;

            if (empty($jobListings)) {
                $this->addFlash('warning', 'No matching job listings found');
            }

            if (!empty($jobListings)) {
                $this->addFlash('success', 'Found job listings with the keyword ' .$input);
            }

            return $this->redirectToRoute('app_homepage',
                [
//                    'JobsSearchForm' => $searchForm->createView(),
                    'jobListings' => $jobListings
                ]
            );
        }

        $allJobListings = $entityManager
            ->getRepository(JobListing::class)
            ->findAll();
        ;

        return $this->render('views/homepage.html.twig',
            [
                'JobsSearchForm' => $searchForm->createView(),
                'jobListings' => $allJobListings
            ]
        );
    }


    #[Route('/candidate/{jobsId}/details',
        name: 'app_homepage_details_page',
        methods: ['GET'
        ])
    ]
    public function detailsPage($jobsId, EntityManagerInterface $entityManager) : Response
    {
        $job = $entityManager
            ->getRepository(JobListing::class)
            ->findOneBy(
                [
                    'id' => $jobsId
                ]
            );
//        dd($jobsId);

        return $this->render(
            'views/candidates-job-details.html.twig',
            [
                'job' => $job
            ]
        );
    }

    #[Route('/candidate/{jobsId}/details/apply', name: 'app_apply', methods: ['POST', 'GET'])]
    public function apply(
        $jobsId,
        Request $request,
        EntityManagerInterface $entityManager,
    ) : Response
    {
        $candidateEmail = $this->getUser()->getUserIdentifier();
        $candidate = $entityManager
            ->getRepository(Candidate::class)
            ->findOneBy(
                [
                    'email'  => $candidateEmail
                ]
            );

        $job = $entityManager
            ->getRepository(JobListing::class)
            ->find([
                'id' => $jobsId
            ]);

        $application = new Application();
        $applicationForm = $this->createForm(ApplicationFormType::class);
        $applicationForm->handleRequest($request);

        if($applicationForm->isSubmitted() && $applicationForm->isValid())
        {
            $name = $applicationForm->get('applicantName')->getData();
            $email = $applicationForm->get('applicantEmail')->getData();
            $phoneNumber = $applicationForm->get('applicantPhoneNumber')->getData();
            $resume = $applicationForm->get('resume')->getData();
            $coverLetter = $applicationForm->get('coverLetter')->getData();

            $application->setApplicantName($name);
            $application->setApplicantEmail($email);
            $application->setApplicantPhoneNumber($phoneNumber);
            $application->setSubmittdAt(new \DateTimeImmutable());
            $application->setCandidateId($candidate);
            $application->setStatus('onWait');
            $application->setJobListingId($job);

            // save the dir where we want to upload our file
            $uploadDirectory = 'C:\xampp\htdocs\uploads';

            // generate a unique name
            $newFilenameResume = uniqid().'.pdf';
            $newFilenameCoverLetter = uniqid().'.pdf';

            // move the file (s) to the desired directory
            $resume->move($uploadDirectory, $newFilenameResume);
            $coverLetter->move($uploadDirectory, $newFilenameCoverLetter);

            // get the content out of the file
            $fileContentsResume = file_get_contents($uploadDirectory.'/'.$newFilenameResume);
            $fileContentsCoverLetter = file_get_contents($uploadDirectory.'/'.$newFilenameCoverLetter);

            // save the content to the database as blob
            $application->setResume($fileContentsResume);
            $application->setCoverLetter($fileContentsCoverLetter);

            // persist and flush data
            $entityManager->persist($application);
            $entityManager->flush();

            if (!$applicationForm->isSubmitted()) {
                $this->addFlash('warning', 'Application failed to submit');
            }

            if ($applicationForm->isSubmitted()) {
                $this->addFlash('success', 'Application successfully submitted');
            }

            return $this->redirectToRoute('app_homepage');

        }

        return $this->render(
            'views/candidate-apply.html.twig',
            [
                'jobsId' => $jobsId,
                'applicationForm' => $applicationForm->createView(),
            ]
        );
    }
}