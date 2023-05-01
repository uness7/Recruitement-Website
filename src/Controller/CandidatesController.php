<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Candidate;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CandidatesController extends AbstractController
{
    #[Route('/candidate', name: 'app_candidate', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $candidateEmail = $this->getUser()->getUserIdentifier();
        $candidate = $entityManager
            ->getRepository(Candidate::class)
            ->findOneBy(
                [
                    'email' => $candidateEmail
                ]
            );
        $applications = $entityManager
            ->getRepository(Application::class)
            ->findBy(
                [
                    'candidateId' => $candidate
                ]
            );



        return $this->render('views/candidates.html.twig',
            [
                'candidate' => $candidate,
                'applications' => $applications
            ]
        );
    }


    #[Route('/candidate/updateProfile', name: 'app_candidates_updateinfo', methods: ['GET', 'POST'])]
    public function updateInfo(
        EntityManagerInterface $entityManager,
        SessionInterface $session): Response
    {
        $candidateEmail = $this->getUser()->getUserIdentifier();
        $user = $entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $candidateEmail]);
        $candidate = $entityManager->getRepository(Candidate::class)
            ->findOneBy(['email' => $candidateEmail]);



        if( $_SERVER['REQUEST_METHOD'] == 'POST') {
            $firstName = $_POST['first-name'];
            $lastName = $_POST['last-name'];
            $email = $_POST['email'];
            $resume = $_POST['resume'];
            $phoneNumber = $_POST['phone'];
            if (!$email || !$phoneNumber || !$resume || !$lastName || !$firstName ) {
                $session->getFlashBag()->add('failure', 'Sorry, but all fields are required.');
                return $this->redirectToRoute('app_candidates_updateinfo');
            }
            // persisting updated date to the database
            $candidate->setFirstName($firstName);
            $candidate->setEmail($email);
            $candidate->setLastName($lastName);
            $candidate->setPhoneNumber($phoneNumber);
            $candidate->setResume($resume);

            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setEmail($email);

            $entityManager->persist($candidate);
            $entityManager->persist($user);
            $entityManager->flush();

            $session->getFlashBag()->add('success', 'Your profile\'s info has been updated.');
        }
        return $this->render('views/candidates-update-profile.html.twig');
    }


    #[Route('/candidate/cv-builder', name: 'app_candidates_cv_builder', methods: ['GET'])]
    public function CVBuilder() : Response
    {
        return $this->render('views/candidates_cv_builder.html.twig');
    }


    #[Route('/candidate/{jobsId}/cancel', name: 'app_candidates_cancel_application', methods: ['POST', 'GET'])]
    public function cancelApplication($jobsId, EntityManagerInterface $entityManager) : RedirectResponse
    {
        $application = $entityManager
            ->getRepository(Application::class)
            ->findOneBy(
                [
                    'jobListingId' => $jobsId
                ]
            );

        $entityManager->remove($application);
        $entityManager->flush();

        $this->addFlash('success', 'Application deleted successfully.');

        return $this->redirectToRoute('app_candidate');
    }

}