<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Recruiter;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class RecruitersController extends AbstractController
{
    #[Route('/recruiter', name: 'app_recruiter', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $recruiterEmail = $this->getUser()->getUserIdentifier();
//        dd($recruiter);
        $recruiter = $entityManager
            ->getRepository(Recruiter::class)
            ->findOneBy([
                'email' => $recruiterEmail
            ]);
        $recruiterId = $recruiter->getId();
//        dd($recruiterId);
        return $this->render('views/recruiters.html.twig',
        [
            'recruiterId' => $recruiterId
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
//        dd($recruiter);
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
//        dd($candidates);
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


    #[Route('/recruiter/update-profile', name: 'app_recruiters_updateprofile', methods: ['GET', 'POST'])]
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

}
