<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="MeetEntry")
 */
class MeetEntry {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Athlete")
     * @ORM\JoinColumn(name="athlete_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $athlete;

    /**
     * @ORM\ManyToOne(targetEntity="Meet")
     * @ORM\JoinColumn(name="meet_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $meet;

    /**
     * @ORM\ManyToOne(targetEntity="MeetEvent")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $event;
}

?>