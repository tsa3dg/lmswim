<?php

namespace App\Controller;

use App\Entity\MeetEvent;
use App\Entity\MeetRecord;
use App\StaticClasses\LMClient;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class RecordController extends Controller {
    /**
     * @Route("/records", name="records_list")
     */
    public function recordsList() {
        return $this->render('records.html.twig', array());
    }

    /**
     * @Route("/admin/records/client/update")
     */
    /*
    public function recordsClientUpdate(ClientController $client) {
        $repoEvt = $this->getDoctrine()->getRepository(MeetEvent::class);
        $repoRecord = $this->getDoctrine()->getRepository(MeetRecord::class);
        $em = $this->getDoctrine()->getManager();

        $clientRecords = $client->executeTasks();

        if($clientRecords["status"] != 0){
            return new JsonResponse(array(
                "records" => $clientRecords["output"],
                "error_msg" => $clientRecords["status"],
            ));
        }

        $records = json_decode($clientRecords["output"]);

        $dnd_id_list = array();

        foreach($rec as $record) {
            $inEvent = $rec->RecEvent;
            $event = new MeetEvent($inEvent->Id, $inEvent->StrokeName, $inEvent->Sex, 
                $inEvent->Distance, $inEvent->AgeGroup, $inEvent->Order);

            $existingEvt = $repoEvt->findOneById($event->Id);
            if($existingEvt == null){
                $em->merge($event);
            }else{
                $em->persist($event);
            }
            $em->flush();

            $record = new Record($event, $rec->RecDate, $rec->RecTime, $rec->RecText);
            
            $existingRec = $repoRecord->findOneBy(array('event' => $event));
            if($existingRec == null) {
                $em->merge($existingRec);
            } else {
                $em->persist($existingRec);
            }
            $em->flush();

            $dnd_id_list[] = $event->Id;
        }
        $em->flush();

        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $qb->delete('MeetEvent', 'm')
            ->where(
                $qb->expr()->notIn('m.id', $dnd_id_list)
            );
        $em->flush();

        $em->clear();

        return new JsonResponse(array(
            "output" => "Records updated!",
            "status" => 0,
        ));
    }
    */
}

?>