<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RecruitersController extends AbstractController
{
    #[Route('/recruiter', name: 'app_recruiter', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('views/recruiters.html.twig');
    }
}