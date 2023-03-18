<?php

namespace App\Controller;

use App\Entity\Candidate;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\PDFScorer;

class PDFController extends AbstractController
{

    /**
     * @throws \Exception
     */
    #[Route('/candidate/create-resume', name: 'app_candidate_resume', methods: ['GET', 'POST'])]
    public function viewResume(
        EntityManagerInterface $entityManager,
        SessionInterface $session,
//        Request $request,
//        PDFScorer $pdfScorer
    ): Response
    {
        $candidateId = $this->getUser()->getUserIdentifier();
        $candidate = $entityManager
            ->getRepository(Candidate::class)
            ->findOneBy(['email' => $candidateId]);


        if(isset($_POST['first-name']) AND $_SERVER['REQUEST_METHOD'] == 'POST')
        {
            $fname = $_REQUEST['first-name'];
//            $lname = $_REQUEST['last-name'];
//            $email = $_REQUEST['email'];
//            $bd = $_REQUEST['bd'];
//            $sex = $_REQUEST['sex'];
//            $city = $_REQUEST['city];
//            $phoneNumber = $_REQUEST['phone'];
//            $language = $_REQUEST['language'];
//            $language_level = $_REQUEST['language-level];
//            $socialMedia = $_REQUEST['social-media'];
//            $info = $_REQUEST['info'];




            $content = '<p>Name:' . $fname . '</p>' ;
            $dompdf = new Dompdf;
            $dompdf->loadHtml($content);
            $dompdf->render();

            ob_end_clean();
//            $dompdf->stream('document-fails.pdf', ['Attachment' => 0]);
            $pdf = $dompdf->output();


//            $score = $pdfScorer->scorePdf($pdf);


            // persist the data into the database
            $candidate->setResume($pdf);
//            $candidate->setScore($score);
            $entityManager->persist($candidate);
            $entityManager->flush();

            // send the user a lil message
            $session->getFlashBag()->add('success', 'Your PDF has been generated.');
        }
        return $this->render('views/candidates_second_page.html.twig');
    }


//    #[Route('/candidate/create-resume/show-pdf', name: 'app_pdf_showpdf', methods: ['GET'])]
//    public function showPDF(EntityManagerInterface $entityManager): Response
//    {
//        $candidateId = $this->getUser()->getUserIdentifier();
////        dd($candidateId);
//        $candidate = $entityManager
//            ->getRepository(Candidate::class)
//            ->findOneBy(['email' => $candidateId]);
//        $response = new Response($candidate->getResume());
//        $response->headers->set('Content-Type', 'application/pdf');
//        $response->headers->set('Content-Disposition', 'inline; filename="' . $candidate->getFirstName() . '_resume.pdf"');
//        return $response;
//    }
}