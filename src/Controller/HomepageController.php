<?php

namespace App\Controller;

use App\Controller\Queries\HomepageSearchData;
use App\Entity\JobListing;
use App\Form\JobsSearchFromType;
use Doctrine\ORM\EntityManagerInterface;
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
    public function detailsPage($jobsId) : Response
    {
        return $this->render(
            'views/candidates-job-details.html.twig',
            [
                'jobsId' => $jobsId
            ]
        );
    }
}