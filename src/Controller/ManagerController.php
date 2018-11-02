<?php

namespace App\Controller;

use App\Entity\Athlete;
use App\Entity\Team;
use App\Entity\Meet;
use App\Entity\AttendanceEntry;
use App\Service\FileHandler;
use App\Service\ClientHandler;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use \DateTime;

class ManagerController extends Controller {
    
    /**
     * @Route("/manager", name="manager_list")
     */
    public function managerList() {
        return $this->render('manager.html.twig', array(
        ));
    }

    /**
     * @Route("/manager/api/athlete/update")
     */
    public function updateAthletesAPI(Request $request) {
        $log = $this->updateAthletes(json_decode($request->getContent()));

        return new JsonResponse(array(
            'log' => $log,
        ));
    }

    /**
     * @Route("/manager/upload", name="manager_upload")
     */
    public function managerUpload(Request $request, FileHandler $fileHandler, ClientHandler $clientHandler) {

        $form = $this->createFormBuilder()
                    ->add('updateAthletes', CheckboxType::class, array(
                        'required' => false,
                    ))
                    ->add('updateRecords', CheckboxType::class, array(
                        'required' => false,
                    ))
                    ->add('managerDatabase', FileType::class)
                    ->add('submit', SubmitType::class, array(
                        'attr' => array('class' => 'btn-primary'))
                    )
                    ->getForm();
        
        $form->handleRequest($request);

        if($form->isSubmitted()) {
            if($form->isValid()) {
                $data = $form->getData();

                $tm_database = $data['managerDatabase'];

                $tm_database_path = $this->container->getParameter('tm_database_path');

                $extractPath = $tm_database_path.hash('sha256', time()).'/';
                $tm_database = $fileHandler->unzipArchive($tm_database->getRealPath(), $extractPath, "MDB");
                $output = $clientHandler->run($tm_database, $data['updateRecords'], $data['updateAthletes']);

                if($output[2] != "") {
                    // Handle error
                }else{
                    $jsonObjects = json_decode($output[1]);

                    $log = array();
                    foreach($jsonObjects as $object) {
                        if($object->Identifier == "Athletes") {
                            $log["Athletes"] = $this->updateAthletes($object->Objects);
                        } else if($object->Identifier == "Records") {
                            //$this->updateRecords($object->Objects);
                        }  
                    }

                    return new JsonResponse($log);
                }
            }
        }

        return $this->render('form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    private function updateRecords() {
        // TODO
    }

    private function updateAthletes($athletes) {
        $repoAth = $this->getDoctrine()->getRepository(Athlete::class);
        $repoTeam = $this->getDoctrine()->getRepository(Team::class);
        $repoMeet = $this->getDoctrine()->getRepository(Meet::class);
        $em = $this->getDoctrine()->getManager();

        $log = array();

        $dnd_id_list = array();
        $team = $repoTeam->findOneBy(array(
            'code' => $this->container->getParameter('team_code'),
        ));
        $meets = $repoMeet->findAll();

        foreach($athletes[0]->Objects as $ath) {
            $athlete = new Athlete($ath->Id, $ath->First, $ath->Last, 
                new DateTime($ath->Birth), $ath->IdNo, $ath->Sex, $team);        

            $existing = $repoAth->findOneBy(array("id" => $ath->Id));

            if($existing != null) {
                if(!$existing->compare($athlete)) {
                    $em->merge($athlete);
                    $log[] = "Updated athlete info for {$athlete->getFullName()}";
                }
            } else {
                $em->persist($athlete);
                $em->flush();

                foreach($meets as $meet){
                    $attendanceEntry = new AttendanceEntry($athlete, $meet, true, null);
                    $em->persist($attendanceEntry);
                }
                $log[] = "Added athlete {$athlete->getFullName()}";
            }
            $em->flush();

            $dnd_id_list[] = $ath->Id;
        }       

        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $athletesToRemove = $qb->select('a')->from('App\Entity\Athlete', 'a')
            ->where(
                $qb->expr()->notIn('a.id', $dnd_id_list)
            )
            ->getQuery()
            ->getResult();
        foreach($athletesToRemove as $ath) {
            $em->remove($ath);
            $log[] = "Deleted athlete {$ath->getFullName()}";
        }

        $em->flush();
        $em->clear();

        return $log;
    }
}

?>