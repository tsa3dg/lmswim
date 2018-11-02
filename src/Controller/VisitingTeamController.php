<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class VisitingTeamController extends Controller {
    /**
     * @Route("/visiting_team", name="visiting_team")
     */
    public function visitingTeam() {
        return $this->render('visiting_team.html.twig');
    }
}

?>