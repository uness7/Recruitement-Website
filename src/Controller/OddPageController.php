<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OddPageController extends AbstractController
{
    #[Route('/new-page', name: 'new_page')]
    public function index(): Response
    {
        return new Response('this  is a controlled page only for candidates');
    }

}