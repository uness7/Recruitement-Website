<?php

namespace App\Controller;

use App\Entity\Candidate;
use App\Entity\Recruiter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecruitersController extends AbstractController
{
    #[Route('/recruiter', name: 'app_recruiter', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('views/recruiters.html.twig');
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
//        dd($content);

        ob_clean();
        $response = new Response();
        $response->headers->set('Content-Type', 'application/pdf');
        $response->headers->set('Content-Disposition', 'inline; filename="' . $candidate->getFirstName() . '_resume.pdf"');
//        $response->headers->set('Content-Length', strlen(stream_get_contents($candidate->getResume())));
//        $response->headers->set('Content-Transfer-Encoding', 'binary');
        $response->setContent($content); // $pdfContent is the binary content of the PDF file
        return $response;

    }
}
