<?php

namespace App\Controller;

use Dompdf\Dompdf;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
class ResumeController extends AbstractController
{
    public function calculateResumeScore($pdfBlob): int
    {
        $dompdf = new Dompdf();
        $dompdf->loadHtml($pdfBlob);
        $dompdf->render();
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
        return $score;
    }


}