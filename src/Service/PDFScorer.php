<?php
//
//namespace App\Controller;
//namespace App\Service;
//
//use Exception;
//use Smalot\PdfParser\Parser;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//
//class PDFScorer extends AbstractController
//{
//    private Parser $pdfParser;
//
//    public function __construct(Parser $pdfParser)
//    {
//        $this->pdfParser = $pdfParser;
//    }
//
//    /**
//     * @throws Exception
//     */
//    public function scorePdf(string $pathToPdf): int
//    {
//        $parser = new Parser();
//        $pdf = $parser->parseFile($pathToPdf);
//        $text = $pdf->getText();
//        $score = 0;
//
//        // Define criteria and corresponding keywords
//        $criteria = [
//            'work_experience' => ['project management', 'team leadership', 'client relations'],
//            'education' => ['degree', 'certification', 'training'],
//            'skills' => ['programming', 'data analysis', 'communication'],
//            'name' => ['georgina', 'john', 'jack']
//        ];
//        // Score skills
//        $skillsScore = 0;
//        foreach ($criteria['skills'] as $keyword) {
//            $occurrences = substr_count(strtolower($text), strtolower($keyword));
//            $skillsScore += $occurrences;
//        }
//
//
//        // Score education
//        $educationScore = 0;
//        foreach ($criteria['education'] as $keyword) {
//            $occurrences = substr_count(strtolower($text), strtolower($keyword));
//            $educationScore += $occurrences;
//        }
//        return $educationScore + $skillsScore;
//    }
//}
