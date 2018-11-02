<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class ResultController extends Controller {
    /**
     * @Route("/results", name="results_list")
     */
    public function resultsList() {
        return $this->render('results.html.twig', array());
    }
}

?>