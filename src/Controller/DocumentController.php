<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentController extends Controller {
    /**
     * @Route("/docs/parking", name="docs_parking")
     */
    public function docsParking() {
        return new BinaryFileResponse('docs/parking.pdf');
    }
}

?>