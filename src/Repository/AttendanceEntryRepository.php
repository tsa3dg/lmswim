<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class AttendanceEntryRepository extends EntityRepository {

    public function findAll() {
        $conn = $this->getEntityManager()->getConnection();

        $stmt = $conn->prepare("SELECT ath.id AS aid, att.id As id, ath.first, ath.last, att.attending FROM 
        AttendanceEntry att LEFT JOIN Athlete ath ON att.athlete_id = ath.id LEFT JOIN 
        Meet meet ON meet.id = att.meet_id ORDER BY ath.last, ath.first, meet.start");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

?>