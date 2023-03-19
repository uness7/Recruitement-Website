<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CandidatesController extends AbstractController
{
    #[Route('/candidate', name: 'app_candidate', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('views/candidates.html.twig');
    }

    #[Route('/candidate/updateProfile', name: 'app_candidates_updateinfo', methods: ['GET', 'POST'])]
    public function updateInfo(EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $candidateEmail = $this->getUser()->getUserIdentifier();
        $user = $entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $candidateEmail]);
//        dd($candidateEmail);
        $candidate = $entityManager->getRepository(Candidate::class)
            ->findOneBy(['email' => $candidateEmail]);
//        dd($candidate);
        // let's get the data from the request
        if( $_SERVER['REQUEST_METHOD'] == 'POST') {
            $firstName = $_POST['first-name'];
            $lastName = $_POST['last-name'];
            $email = $_POST['email'];
            $resume = $_POST['resume'];
            $phoneNumber = $_POST['phone'];
            if (!$email || !$phoneNumber || !$resume || !$lastName || !$firstName ) {
                // One or more required fields are missing
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

            // persist data and flush
            $entityManager->persist($candidate);
            $entityManager->persist($user);
            $entityManager->flush();

            // display a lil message to the end user
            $session->getFlashBag()->add('success', 'Your profile\'s info has been updated.');
        }
        return $this->render('views/candidates-update-profile.html.twig');
    }
}