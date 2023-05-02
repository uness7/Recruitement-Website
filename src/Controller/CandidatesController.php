<?php

namespace App\Controller;

use App\Entity\Application;
use App\Entity\Candidate;
use App\Entity\User;
use App\Form\CandidateUpdateFormType;
use App\Form\ResumeFormType;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
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
                'applications' => $applications,
            ]
        );
    }


    #[Route('/candidate/updateProfile', name: 'app_candidates_updateinfo', methods: ['GET', 'POST'])]
    public function updateInfo(
        EntityManagerInterface $entityManager,
        SessionInterface $session,
        Request $request,
    ): Response
    {
        $candidateEmail = $this->getUser()->getUserIdentifier();
        $user = $entityManager
            ->getRepository(User::class)
            ->findOneBy(['email' => $candidateEmail]);
        $candidate = $entityManager->getRepository(Candidate::class)
            ->findOneBy(['email' => $candidateEmail])
        ;

        $form = $this->createForm(CandidateUpdateFormType::class);
        $form->handleRequest($request);

        $photo = $form->get('photo')->getData();
        $firstName = $form->get('firstName')->getData();
        $lastName = $form->get('lastName')->getData();
        $email = $form->get('email')->getData();
        $phoneNumber = $form->get('phoneNumber')->getData();

        if($form->isSubmitted() && $form->isValid()) {
            $candidate->setFirstName($firstName);
            $candidate->setEmail($email);
            $candidate->setLastName($lastName);
            $candidate->setPhoneNumber($phoneNumber);

//            dd(filetype($photo));
            $updatedDir = 'C:\xampp\htdocs\uploads';
            $photoNewName = uniqid().'.jpg';
            $photo->move($updatedDir, $photoNewName);
            $photoContent = file_get_contents($updatedDir.'/'.$photoNewName);
            $candidate->setPhoto($photoContent);

            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setEmail($email);

            $entityManager->persist($candidate);
            $entityManager->persist($user);
            $entityManager->flush();

            $session->getFlashBag()->add('success', 'Your profile\'s info has been updated.');

//            return $this->redirectToRoute();
        }


        return $this->render('views/candidates-update-profile.html.twig',
            [
                'updateInfoForm' => $form->createView(),
            ]
        );
    }


    #[Route('/candidate/cv-builder', name: 'app_candidates_cv_builder', methods: ['GET', 'POST'])]
    public function CVBuilder(Request $request) : Response
    {
        $dompdf = new Dompdf();

        $form = $this->createForm(ResumeFormType::class);
        $form->handleRequest($request);

        $firstName = $form->get('firstName')->getData();
        $lastName = $form->get('lastName')->getData();
        $email = $form->get('email')->getData();
        $phoneNumber = $form->get('phoneNumber')->getData();
        $birthdate = $form->get('birthdate')->getData();
        $address = $form->get('address')->getData();

        if($form->isSubmitted() && $form->isValid()) {
            $html = $this->renderView('pdf_template.html.twig', [
                'firstName' => $firstName,
                'lastName' => $lastName,
                'email' => $email,
                'phoneNumber' => $phoneNumber,
                'birthdate' => $birthdate,
                'address' => $address,
            ]);
            $dompdf->loadHtml($html);
            $dompdf->setPaper('A4', 'portrait');
            $dompdf->render();
            return new Response($dompdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="your_pdf_name.pdf"',
            ]);


        }

        return $this->render('views/candidates_cv_builder.html.twig',
            [
                'resumeForm' => $form->createView(),
            ]
        );
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