<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class MeetRepository extends EntityRepository {

    public function findAll() {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('m')->from('App\Entity\Meet', 'm')
            ->orderBy('m.start', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function getNextMeet() {
        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('m')->from('App\Entity\Meet', 'm')
            ->where(
                $qb->expr()->gte('m.start', 'CURRENT_DATE()')
            )
            ->orderBy('m.start', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}

?>