<?php

namespace App\Controller;

use App\Util\SetUtility;
use App\Util\DateUtility;
use App\Entity\ExternalEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use \DateTime;

class EventController extends Controller {

    /**
     * @Route("/events", name="events_calendar")
     */
    public function eventsCalendar() {
        return $this->render('events.html.twig', array());
    }

    /**
     * @Route("/events/from", name="get_events_by_year_and_month_noid")
     * @Route("/events/from/{year}/{month}", name="get_events_by_year_and_month")
     */
    public function getEventsByYearAndMonth($year, $month) {
        $repo = $this->getDoctrine()->getRepository(ExternalEvent::class);

        $events = $repo->getEventsByYearAndMonth($year, $month);
        $eventObjects = array();

        $daysInMonth = DateUtility::getDaysInMonth($year, $month);

        foreach($events as $event) {

            $repeat = $event->getRepeat();
            $startDate = $event->getStart();
            $endDate = $event->getEndRepeat();
            
            if($repeat == ExternalEvent::REPEAT_NONE || 
                $repeat == ExternalEvent::REPEAT_MONTHLY ||
                $repeat == ExternalEvent::REPEAT_YEARLY) {
                SetUtility::addObjToSet($eventObjects, $startDate->format('j'), $event);
            } else if($repeat == ExternalEvent::REPEAT_DAILY) {
                $startDay = $startDate->format('n') == $month ?
                    $startDate->format('j') : 1;
                $endDay = (!is_null($endDate) && $endDate->format('n') == $month) ? 
                    $endDate->format('j') : $daysInMonth;
                for($i = $startDay; $i < $endDay; $i++) {
                    SetUtility::addObjToSet($eventObjects, $i, $event);
                }
            } else if($repeat == ExternalEvent::REPEAT_WEEKLY) {
                $startDateDow = $startDate->format('w');
                $firstOfMonthDow = DateTime::createFromFormat('Y-m-d', "$year-$month-01")->format('w');
                $incrementFromFirst = ($startDateDow > $firstOfMonthDow) ? 
                    (7 - $startDateDow + $firstOfMonthDow) : 
                    ($firstOfMonthDow - $startDateDow);
                
                    $startDay = 1 + $incrementFromFirst;
                    $endDay = (!is_null($endDate) && $endDate->format('n') == $month) ? 
                        $endDate->format('j') : $daysInMonth;
                    for($i = $startDay; $i < $endDay; $i+=7) {
                        SetUtility::addObjToSet($eventObjects, $i, $event);
                    }
            }
        }

        return new JsonResponse(array(
            'events' => $eventObjects,
        ));
    }

    /**
     * @Route("/event/delete/{id}", name="delete_event")
     * @Route("/event/delete", name="delete_event_noid")
     */
    public function deleteEvent($id = -1) {

        $success = false;
        if($id == -1) {
            $repo = $this->getDoctrine()->getRepository(ExternalEvent::class);
            $event = $repo->getOneById($id);
            if(!is_null($event)) {
                $em->remove($event);
                $em->flush();
                $success = true;
            }
        }

        return new JsonResponse(array('SUCCESS' => $success));
    }

    /**
     * @Route("/event/update/{id}", name="update_event")
     * @Route("/event/update", name="update_event_noid")
     */
    public function updateEvent($id = -1, Request $request){
        $em = $this->getDoctrine()->getManager();
        if($id == -1){
            $event = new ExternalEvent();
        }else{
            $repo = $this->getDoctrine()->getRepository(ExternalEvent::class);
            $event = $repo->findOneById($id);
        }

        if($id == -1) {
            $defaultEndDay = new DateTime('now');
            $defaultEndDay->setTime(23,59);
        } else if(!$event->hasEnd()) {
            $defaultEndDay = clone $event->getStart();
            $defaultEndDay->setTime(23,59);
        }else {
            $defaultEndDay = $event->getEnd();
        }

        $form = $this->createFormBuilder()
                    ->setAction($request->getUri())
                    ->add('title', TextType::class, array(
                        'data' => $event->getTitle(),
                    ))
                    ->add('allDay', CheckboxType::class, array(
                        'data' => $event->isAllDay(),
                        'required' => false,
                    ))
                    ->add('start', DateTimeType::class, array(
                        'data' => $id == -1 ? new DateTime('now') : $event->getStart(),
                    ))
                    ->add('hasEnd', CheckboxType::class, array(
                        'data' => $event->hasEnd(),
                        'required' => false,
                    ))
                    ->add('end', DateTimeType::class, array(
                        'data' => $defaultEndDay,
                        'required' => false,
                    ))
                    ->add('repeat', ChoiceType::class, array(
                        'choices' => array(
                            'None' => 0,
                            'Every Day' => 1,
                            'Every Week' => 2,
                            'Every Month' => 3,
                            'Every Year' => 4,
                        ),
                        'data' => $id == -1 ? ExternalEvent::REPEAT_NONE : $event->getRepeat(),
                    ))
                    ->add('endRepeat', DateType::class, array(
                        'required' => false,
                        'data' => $event->getEndRepeat(),
                    ))
                    ->add('location', TextType::class, array(
                        'data' => $event->getLocation(),
                    ))
                    ->add('desc', TextareaType::class, array(
                        'required' => false,
                        'data' => $event->getDesc(),
                    ))
                    ->add('isHoliday', CheckboxType::class, array(
                        'required' => false,
                        'label' => 'Is Event a holiday?',
                        'data' => $event->getIsHoliday(),
                    ))
                    ->add('save', SubmitType::class, array('attr' => array('class' => 'btn-primary')))
                    ->add('cancel', ButtonType::class, array('attr' => array('class' => 'btn-secondary', 'data-dismiss' => 'modal')))
                    ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted()){
            $success = false;
            if($form->isValid()){
                $data = $form->getData();
                
                $event->setTitle($data['title']);
                $event->setStart($data['start']);
                if($data['allDay']) {
                    $event->setAllDay();
                } else {
                    $event->setEnd($data['allDay']);
                    if($event->isAllDay()) {
                        $event->setAllDay();
                    }
                }
                $event->setRepeat($data['repeat']);
                $event->setEndRepeat($data['endRepeat']);
                $event->setLocation($data['location']);
                $event->setDesc($data['desc']);
                $event->setIsHoliday($data['isHoliday']);

                if($id == -1) {
                    $em->persist($event);
                } else {
                    $em->merge($event);
                }
                $em->flush();
                $success = true;
            }
            return new JsonResponse(array('SUCCESS' => $success));
        }

        return $this->render('form.html.twig', array(
            'form' => $form->createView(),
        ));
    }
    
}

?>