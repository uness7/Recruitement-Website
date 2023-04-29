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


class PDFController extends AbstractController
{


    #[Route('/candidate/create-resume', name: 'app_candidate_resume', methods: ['GET', 'POST'])]
    public function viewResume(
        EntityManagerInterface $entityManager,
        SessionInterface $session,
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

            $pdfText = $dompdf->output([], 'S');

            $keywords = [
                'programming' => 10,
                'communication' => 5,
                'teamwork' => 5,
                'problem-solving' => 7,
                'leadership' => 8
            ];

            $score = 0;
            foreach ($keywords as $keyword => $keywordScore) {
                $count = substr_count(strtolower($pdfText), $keyword);
                $score += $count * $keywordScore;
            }


            // persist the data into the database
            $candidate->setResume($pdf);
            $candidate->setScore($score);
            $entityManager->persist($candidate);
            $entityManager->flush();

            // send the user a lil message
            $session->getFlashBag()->add('success', 'Your PDF has been generated.');
        }
        return $this->render('candidates_cv_builder.html.twig');
    }
}