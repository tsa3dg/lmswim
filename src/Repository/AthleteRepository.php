<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class AthleteRepository extends EntityRepository {

    public function findAll() {
        $qb =  $this->getEntityManager()->createQueryBuilder();
        return $qb->select('a')->from('App\Entity\Athlete', 'a')
                    ->orderBy('a.last', 'ASC')
                    ->getQuery()
                    ->getResult();
    }

    public function findAttendeesByMeet($meet_id) {
        $conn = $this->getEntityManager()->getConnection();

        $stmt = $conn->prepare("SELECT ath.first As first, ath.last As last, ath.sex As sex, 
        ath.birthday As birthday FROM Athlete ath LEFT JOIN AttendanceEntry att ON att.athlete_id = ath.id 
        LEFT JOIN MeetEvent meet ON meet.id = att.meet_id WHERE att.meet_id = :meet_id AND 
        att.attending = '1' ORDER BY ath.last, ath.first");
        $stmt->bindValue('meet_id', $meet_id);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findAbsenteesOrHasNotesByMeet($meet_id) {
        $conn = $this->getEntityManager()->getConnection();

        $stmt = $conn->prepare("SELECT ath.first AS first, ath.last AS last, att.notes As notes, 
        att.attending FROM Athlete ath LEFT JOIN AttendanceEntry att ON att.athlete_id = ath.id LEFT JOIN 
        MeetEvent meet ON meet.id = att.meet_id WHERE att.meet_id = :meet_id AND 
        (att.attending = '0' OR att.notes IS NOT NULL) ORDER BY ath.last, ath.first");
        $stmt->bindValue('meet_id', $meet_id);
        $stmt->execute();

        return $stmt->fetchAll();
    }
}

?>