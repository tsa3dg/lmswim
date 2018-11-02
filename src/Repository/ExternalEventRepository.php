<?php

namespace App\Repository;

use App\Util\DateUtility;
use DoctrineExtensions\Query\Mysql;
use Doctrine\ORM\EntityRepository;
use App\Entity\ExternalEvent;
use \DateTime;

class ExternalEventRepository extends EntityRepository {

    public function getEventsByYearAndMonth($year, $month) {

        $daysInMonth = DateUtility::getDaysInMonth($year, $month);

        $dt_start = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', mktime(0,0,0,$month,1,$year)));
        $dt_end = DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', mktime(23,59,59,$month,$daysInMonth,$year)));

        $qb = $this->getEntityManager()->createQueryBuilder();
        return $qb->select('e')->from('App\Entity\ExternalEvent', 'e')
                    ->where(
                        $qb->expr()->orX(
                            $qb->expr()->between('e.start', ':from', ':to'),
                            $qb->expr()->andX(
                                $qb->expr()->orX(
                                    $qb->expr()->isNull('e.endRepeat'),
                                    $qb->expr()->lte('e.endRepeat', ':to')
                                ),
                                $qb->expr()->eq('e.repeat', ExternalEvent::REPEAT_DAILY),
                                $qb->expr()->eq('e.repeat', ExternalEvent::REPEAT_WEEKLY),
                                $qb->expr()->andX(
                                    $qb->expr()->eq('e.repeat', ExternalEvent::REPEAT_MONTHLY),
                                    $qb->expr()->lte('DAY(e.start)', $daysInMonth)
                                ),
                                $qb->expr()->andX(
                                    $qb->expr()->eq('e.repeat', ExternalEvent::REPEAT_YEARLY),
                                    $qb->expr()->eq('MONTH(e.start)', $month)
                                )
                            )
                        )
                    )
                    ->setParameter('from', $dt_start)
                    ->setParameter('to', $dt_end)
                    ->orderBy('e.start', 'ASC')
                    ->getQuery()
                    ->getResult();
    }

}

?>