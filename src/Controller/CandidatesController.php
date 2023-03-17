<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CandidatesController extends AbstractController
{
    #[Route('/candidate', name: 'app_candidate', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('views/candidates.html.twig');
    }


//    #[Route('/candidate/create-resume', name: 'app_candidate_resume', methods: ['GET'])]
//    public function createCandidateCv(): Response
//    {
//        return $this->render('views/candidates_second_page.html.twig');
//    }
}