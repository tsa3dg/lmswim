<?php

namespace App\Controller;

use App\Entity\ExternalEvent;
use App\Entity\Meet;
use App\Entity\Team;
use App\Service\FileHandler;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use \DateTime;
use \DateInterval;

class ScheduleController extends Controller {

    /**
     * @Route("/schedule", name="schedule_list")
     */
    public function scheduleList() {
        $repoMeet = $this->getDoctrine()->getRepository(Meet::class);
        $repoTeam = $this->getDoctrine()->getRepository(Team::class);

        $team_code = $this->container->getParameter('team_code');

        $meets = $repoMeet->findBy(array(), array('start' => 'ASC'));
        $team = $repoTeam->findOneBy(array('code' => $team_code));

        return $this->render('schedule.html.twig', array(
            'meets' => $meets,
            'team_code' => $this->container->getParameter('team_code'),
            'our_team_id' => $team->getId(),
        ));
    }

    /**
     * @Route("/admin/schedule/delete/{id}", name="delete_schedule")
     * @Route("/admin/schedule/delete", name="delete_schedule_noid")
     */
    public function deleteSchedule($id) {
        $repo = $this->getDoctrine()->getRepository(Meet::class);
        $em = $this->getDoctrine()->getManager();
        $meet = $repo->findOneById($id);
        if($meet != null) {
            $em->remove($meet);
            $em->flush();
            $success = true;
        } else {
            $success = false;
        }
        return new JsonResponse(array('SUCCESS' => $success));
    }

    /**
     * @Route("/admin/schedule/edit/{id}", name="edit_schedule")
     * @Route("/admin/schedule/edit", name="add_schedule")
     */
    public function editSchedule($id = -1, Request $request, FileHandler $fileHandler) {
        $em = $this->getDoctrine()->getManager();
        $repoTeam = $this->getDoctrine()->getRepository(Team::class);
        $repoMeet = $this->getDoctrine()->getRepository(Meet::class);
        $repoEvent = $this->getDoctrine()->getRepository(ExternalEvent::class);
        $qb = $em->createQueryBuilder();

        $lmCode = $this->container->getParameter('team_code');
        $lmElement = $repoTeam->findOneBy(array('code' => $lmCode));
        $otherTeams = $qb->select('t')->from('App\Entity\Team', 't')
                         ->where(
                            $qb->expr()->notIn('t.code', array($lmCode))
                         )
                         ->getQuery()
                         ->getResult();
        if($id == -1){
            $meet = new Meet();
            $opponentData1 = null;
            $opponentData2 = null;
            $locationData = null;
            $start = new DateTime();
            $ageup = new DateTime(getdate()['year'].'-06-01');
        }else{
            $meet = $repoMeet->findOneById($id);
            if($meet->getTeam1()->getCode() == $lmCode) {
                $opponentData1 = $meet->getTeam2();
                $opponentData2 = $meet->getTeam3();
            }else if($meet->getTeam2()->getCode() == $lmCode) {
                $opponentData1 = $meet->getTeam1();
                $opponentData2 = $meet->getTeam3();
            }else {
                $opponentData1 = $meet->getTeam2();
                $opponentData2 = $meet->getTeam3();
            }
            $start = $meet->getStart();
            $ageup = $meet->getAgeUp();
        }

        if($id == -1){
            $locationData = array($lmElement, $otherTeams[0]);
        }else{
            $locationData = array_filter(array($lmElement,$opponentData1,$opponentData2), 
                function($val) { return $val != null; });
        }

        $form = $this->createFormBuilder()
            ->setAction($request->getUri())
            ->add('opponent1', EntityType::class, array(
                'class' => Team::class,
                'choice_label' => 'code',
                'choices' => $otherTeams,
                'data' => $opponentData1
            ))
            ->add('opponent2', EntityType::class, array(
                'class' => Team::class,
                'choice_label' => 'code',
                'choices' => $otherTeams,
                'data' => $opponentData2,
                'required' => false
            ))
            ->add('location', EntityType::class, array(
                'class' => Team::class,
                'choice_label' => 'name',
                'choices' => array_merge(array($lmElement), $otherTeams),
                'data' => $meet->getTeam1(),
            ))
            ->add('start', DateType::class, array(
                'data' => $start
            ))
            ->add('ageUp', DateType::class, array(
                'data' => $ageup
            ))
            ->add('meetProgram', FileType::class, array(
                'label' => 'Meet Program',
                'required' => false,
            ))
            ->add('meetTimeline', FileType::class, array(
                'label' => 'Meet Timeline',
                'required' => false,
            ))
            ->add('resultsName', FileType::class, array(
                'label' => 'Meet Results',
                'required' => false,
            ))
            ->add('scoresName', FileType::class, array(
                'label' => 'Meet Score',
                'required' => false,
            ))
            ->add('save', SubmitType::class, array(
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
                $meet->setTeam1($data['location']);
                if($meet->getTeam1() == $lmElement) {
                    $meet->setTeam2($data['opponent1']);
                    $meet->setTeam3($data['opponent2']);
                }else {
                    $meet->setTeam2($lmElement);
                    if($meet->getTeam1() == $data['opponent1']){
                        $meet->setTeam3($data['opponent2']);
                    }else{
                        $meet->setTeam3($data['opponent1']);
                    }
                }
                $meet->setStart($data['start']);
                $meet->setAgeUp($data['ageUp']);
                
                if($meet->getTeam3() != null) {
                    $leafResultsDir = 'Divisionals/';
                }else{
                    $leafResultsDir = $meet->getTeam2()->getCode().'@'.$meet->getTeam1()->getCode().'/';
                }

                $meet_results_path = $this->container->getParameter('meet_results_path');
                $meet_timeline_path = $this->container->getParameter('meet_timeline_path');
                $meet_program_path = $this->container->getParameter('meet_program_path');
                $meet_scores_path = $this->container->getParameter('meet_scores_path');

                $subPath = $meet->getStart()->format('Y').'/'.$leafResultsDir;

                if(!is_null($data['resultsName'])) {
                    $resultsDir = $fileHandler->unzipArchive($data['resultsName']->getRealPath(), 
                        $meet_results_path.$subPath, "HTML");
                    $meet->setResultsName($resultsDir);
                }

                if(!is_null($data['scoresName'])) {
                    $meet_scores_full_path = $meet_scores_path.$subPath;
                    $data['scoresName']->move($meet_scores_full_path, $data['scoresName']->getClientOriginalName());
                    $meet->setScoresName($meet_scores_full_path.$data['scoresName']->getClientOriginalName());
                }
                
                if(!is_null($data['meetTimeline'])) {
                    $meet_timeline_full_path = $meet_timeline_path.$subPath;
                    $data['meetTimeline']->move($meet_timeline_full_path, $data['meetTimeline']->getClientOriginalName());
                    $meet->setMeetTimelineName($meet_timeline_full_path.$data['meetTimeline']->getClientOriginalName());
                }

                if(!is_null($data['meetProgram'])) {
                    $meet_program_full_path = $meet_program_path.$subPath;
                    $data['meetProgram']->move($meet_program_path.$subPath, $data['meetProgram']->getClientOriginalName());
                    $meet->setMeetProgramName($meet_program_full_path.$data['meetProgram']->getClientOriginalName());
                }

                if($id == -1) {
                    // Create new external event
                    $event = new ExternalEvent();
                    $em->persist($event);
                } else {
                    $event = $repoEvent->findOneBy(array('linkedMeet' => $meet));
                    if(is_null($event)) {
                        // No event linked to meet, create new one
                        $event = new ExternalEvent();
                        $em->persist($event);
                    }
                }

                $event->setTitle($meet->getShortTitle());

                $meetStart = clone $meet->getStart();
                $eventStart = explode(':', $this->container->getParameter('meet_start_time')); 
                $event->setStart($meetStart->add(
                    new DateInterval('PT'.$eventStart[0].'H'.$eventStart[1].'M')));

                if(!$meet->isDivisionals()) {
                    $meet_end_time_str = $this->container->getParameter('meet_end_time');
                } else {
                    $meet_end_time_str = $this->container->getParameter('meet_divisonals_end_time');
                }
                $meetStart = clone $meet->getStart();
                $eventEnd = explode(':', $meet_end_time_str);
                $event->setEnd($meetStart->add(
                    new DateInterval('PT'.$eventEnd[0].'H'.$eventEnd[1].'M')));
                
                $event->setLocation($meet->getTeam1()->getName());

                $event->setIsHoliday(false);
                $event->setRepeat(ExternalEvent::REPEAT_NONE);

                $event->setLinkedMeet($meet);

                if($id == -1){
                    $em->persist($meet);
                }
                $em->flush();               
            }
            return $this->redirectToRoute('schedule_list');
        }

        return $this->render('form.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}
?>