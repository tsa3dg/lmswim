<?php

namespace App\Controller;

use App\Entity\Announcement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use \DateTime;
use \DateInterval;

class AnnouncementController extends Controller {
    
    /**
     * @Route("/", name="home")
     */
    public function listAnnouncements() {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $dt = new DateTime('now');
        $announcements = $qb->select('a')->from('App\Entity\Announcement', 'a')->where(
            $qb->expr()->orX(
                $qb->expr()->isnull('a.exp'),
                $qb->expr()->gt('a.exp', $dt->format("'Y-m-d H:i:s'"))
            )  
        )->orderBy('a.start', 'DESC')->getQuery()->getResult();
        return $this->render('index.html.twig', ['announcements' => $announcements]);
    }

    /**
     * @Route("/admin/announcement/delete/{id}", name="delete_announcement", defaults={"id"=-1})
     */
    public function deleteAnnouncement($id, Request $request) {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(Announcement::class);
        $announcement = $repo->findOneById($id);
        if($announcement != null){
            $em->remove($announcement);
            $em->flush();
        }
        return $this->redirect('/');
    }

    /**
     * @Route("/admin/announcement/edit/{id}", name="edit_announcement")
     * @Route("/admin/announcement/edit", name="edit_announcement_noid")
     */
    public function editAnnouncement($id = -1, Request $request) {
        if($id == -1) {
            $announcement = new Announcement();
        }else{
            $repo = $this->getDoctrine()->getRepository(Announcement::class);
            $announcement = $repo->findOneById($id);
        }

        $defaultExpDate = new DateTime('now');
        $defaultExpDate->add(new DateInterval('P4M'));

        $form = $this->createFormBuilder($announcement)
            ->setAction($request->getUri())
            ->add('start', DateType::class, array('data' => new DateTime('now')))
            ->add('title', TextType::class)
            ->add('atext', TextareaType::class, array('attr' => array('rows' => 10)))
            ->add('exp', DateType::class, array('data' => $defaultExpDate))
            ->add('save', SubmitType::class, array('attr' => array('class' => 'btn-primary')))
            ->add('cancel', ButtonType::class, array('attr' => array('class' => 'btn-secondary', 'data-dismiss' => 'modal')))
            ->getForm();
        $form->handleRequest($request);
        if($form->isSubmitted()) {
            $success = 0;
            if($form->isValid()) {
                $success = 1;
                $announcement = $form->getData();
                $em = $this->getDoctrine()->getManager();
                $em->persist($announcement);
                $em->flush();
            }
            return $this->redirect('/');
        }
        return $this->render('form.html.twig',
            array('form' => $form->createView())
        );
    }
}

?>