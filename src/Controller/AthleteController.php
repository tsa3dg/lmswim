<?php

namespace App\Controller;

use App\Entity\Athlete;
use App\Entity\Team;
use App\Entity\Meet;
use App\Entity\AttendanceEntry;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use \DateTime;

class AthleteController extends Controller {

    /**
     * @Route("/attendance", name="attendance_list")
     */
    public function attendanceList() {
        $repoAttendanceEntry = $this->getDoctrine()->getRepository(AttendanceEntry::class);
        $repoMeet = $this->getDoctrine()->getRepository(Meet::class);
        $repoAthlete = $this->getDoctrine()->getRepository(Athlete::class);

        $attendanceEntries = $repoAttendanceEntry->findAll();
        $meets = $repoMeet->findAll();

        return $this->render('attendance.html.twig', array(
            'attendance_entries' => $attendanceEntries,
            'meets' => $meets,
        ));
    }

    /**
     * @Route("/admin/absentees", name="absentees_list")
     */
    public function absenteeList() {
        $repoAth = $this->getDoctrine()->getRepository(Athlete::class);

        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $meets = $qb->select('m')->from('App\Entity\Meet', 'm')
            ->where(
                $qb->expr()->gte('m.start', 'CURRENT_DATE()')
            )
            ->orderBy('m.start', 'ASC')
            ->getQuery()
            ->getResult();     

        $absentees = array();
        foreach($meets as $meet){
            $meet->absentees = $repoAth->findAbsenteesOrHasNotesByMeet($meet->getId());
        }

        return $this->render('absentees.html.twig', array(
            'meets' => $meets,
        ));
    }

    /**
     * @Route("/attendance/sheet", name="attendance_checkin_sheet")
     */
    public function attendanceCheckinSheet() {
        $repoAth = $this->getDoctrine()->getRepository(Athlete::class);
        $repoMeet = $this->getDoctrine()->getRepository(Meet::class);

        $meet = $repoMeet->getNextMeet();
        $attendees = $repoAth->findAttendeesByMeet($meet->getId());

        $athletes = [];
        foreach($attendees as $athlete) {
            $age = $this->getAgeGroup(new DateTime($athlete["birthday"]));
            $athlete['age'] = $age;
            $athletes[] = $athlete;
        }

        return $this->render('attendance_checkin_sheet.html.twig', array(
            'meet' => $meet,
            'attendees' => $athletes,
        ));
    }

    /**
     * @Route("/attendance/athlete/notes/{id}", name="athlete_notes")
     * @Route("/attendance/athlete/notes", name="athlete_notes_noid")
     */
    public function athleteNotes($id, Request $request) {
        $repoAttendanceEntry = $this->getDoctrine()->getRepository(AttendanceEntry::class);
        $repoAthlete = $this->getDoctrine()->getRepository(Athlete::class);
        
        $attendanceEntries = $repoAttendanceEntry->findBy(array(
            'athlete' => $repoAthlete->findOneById($id),
        ));

        if($request->isMethod('POST')) {
            $em = $this->getDoctrine()->getManager();
            $notes = $request->request->get('form');
            foreach($attendanceEntries as $attendanceEntry) {
                $note = $notes[$attendanceEntry->getMeet()->getId()];
                if(!empty($note)){
                    $attendanceEntry->setNotes($note);
                    $em->merge($attendanceEntry);
                    $em->flush();
                }
            }

            return new JsonResponse(array(
                'SUCCESS' => true,
            ));
        } else {
            $notes = array();
            foreach($attendanceEntries as $attendanceEntry) {
                $notes[] = array(
                    'note' => $attendanceEntry->getNotes(),
                    'meet_id' => $attendanceEntry->getMeet()->getId(),
                );
            }
            return new JsonResponse(array(
                'notes' => $notes,
            ));
        }
    }

    /**
     * @Route("/attendance/athlete/unlock/{id}", name="unlock_athlete")
     * @Route("/attendance/athlete/unlock", name="unlock_athlete_noid")
     */
    public function unlockAthlete($id = -1, Request $request) {
        $repo = $this->getDoctrine()->getRepository(Athlete::class);
        $athlete = $repo->findOneById($id);

        $form = $this->createFormBuilder()
            ->setAction($request->getUri())
            ->add('name', TextType::class, array(
                'disabled' => true,
                'data' => $athlete->getFirst().' '.$athlete->getLast(),
            ))
            ->add('birthday', BirthdayType::class, array(
                'years' => range(date('Y')-19, date('Y')),
            ))
            ->add('Unlock', SubmitType::class, array(
                'attr' => array('class' => 'btn-primary'))
            )
            ->add('cancel', ButtonType::class, array(
                'attr' => array('class' => 'btn-secondary', 'data-dismiss' => 'modal'))
            )
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted()) {
            if($form->isValid()) {
                $data = $form->getData();

                if($athlete != null){
                    if($athlete->getBirth() == $data['birthday']) {
                        $success = true;
                    }else {
                        $success = false;
                    }
                }
            }
            return new JsonResponse(array(
                'SUCCESS' => $success, 
                'ATHLETE_NAME' => $athlete->getFirst().' '.$athlete->getLast(),
            ));
        }
        return $this->render('form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/attendance/athlete/notes/{id}", name="get_athlete_notes")
     * @Route("/attendance/athlete/notes", name="get_athlete_notes_noid")
     */
    public function getAttendanceNotes() {

    }

    /**
     * @Route("/attendance/athlete/update", name="update_athlete_attendance")
     */
    public function updateAthleteAttendance(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repoAttendanceEntry = $this->getDoctrine()->getRepository(AttendanceEntry::class);

        $attendanceEntries = $request->request->get('attendance_entries');
        $athleteBirthday = $request->request->get('birthday');

        $keys = array_keys($attendanceEntries);
        $key = $keys[0];
        $attendanceEntry = $repoAttendanceEntry->find($key);
        $athlete = $attendanceEntry->getAthlete();
        if($athlete->getBirth() != date_create($athleteBirthday)) {
            return new JsonResponse(array(
                'SUCCESS' => false,
                'ERROR_MSG' => 'User is not authorized to update attendance records for this athlete!',
            ));
        }

        $attendanceEntryObjects = array();
        foreach($attendanceEntries as $k => $v) {
            $attendanceEntry = $repoAttendanceEntry->find($k);
            if($athlete != $attendanceEntry->getAthlete()) {
                return new JsonResponse(array(
                    'SUCCESS' => false,
                    'ERROR_MSG' => 'User can only update attendance records for a single athlete at a time!',
                ));
            }
            $attendanceEntryObjects[] = $attendanceEntry;
        }

        $attendings = array_values($attendanceEntries);
        for($i=0; $i<count($attendanceEntryObjects); $i++) {
            $attendanceEntryObjects[$i]->setAttending($attendings[$i]);
            $em->merge($attendanceEntryObjects[$i]);
        }
        $em->flush();

        return new JsonResponse(array(
            'SUCCESS' => true,
            'ATHLETE_NAME' => $athlete->getFirst().' '.$athlete->getLast(),
        ));
    }

    /**************************************************** */
    /*  PRIVATE METHODS                                   */
    /**************************************************** */
    private function getAgeGroup($birth) {
        $now = new DateTime();
        $now->setDate($now->format('Y'),6,1);
        $diff = $now->diff($birth)->y;
        if($diff <= 8){
            return "8 & U";
        }else if($diff <= 10){
            return "9-10";
        }else if($diff <= 12){
            return "11-12";
        }else if($diff <= 14){
            return "13-14";
        }else{
            return "15-18";
        }
    }
}

?>