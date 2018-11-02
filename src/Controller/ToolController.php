<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ToolController extends Controller {

    /**
     * @Route("/tools/scoringcalculator", name="scoring_calculator")
     */
    public function scoringCalculator() {
        return $this->render('scoring_calculator.html.twig', array());
    }

}

?>