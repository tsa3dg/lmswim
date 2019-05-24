<?php

namespace App\Controller;

use App\Entity\Announcement;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use \DateTime;

class AnnouncementController extends Controller {
    
    /**
     * @Route("/announcement", name="fetch_unexpired_announcements")
     */
    public function fetchUnexpiredAnnouncements(SerializerInterface $serializer) {
        $qb = $this->getDoctrine()->getManager()->createQueryBuilder();
        $dt = new DateTime('now');
        $announcements = $qb->select('a')->from('App\Entity\Announcement', 'a')->where(
            $qb->expr()->orX(
                $qb->expr()->isnull('a.expirationtDate'),
                $qb->expr()->gt('a.expirationDate', $dt->format("'Y-m-d H:i:s'"))
            )  
        )->orderBy('a.postDate', 'DESC')->getQuery()->getResult();
        return $serializer->serialize($announcements, 'json'); 
    }

    /**
     * @Route("/announcement/{id}", name="fetch_announcements_by_id")
     */
    public function fetchAnnouncementById($id, Serializer $serializer) {
        $em = $this->getDoctrine()->getManager();
        $query = $em->createQuery('SELECT a FROM App\Entity\Announcement a WHERE a.id = :id');
        $query->setParameter('id', $id);
        $announcement = $query->getSingleResult();
        return $serializer->serialize($announcement, 'json');
    }

}

?>
