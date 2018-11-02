<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="MeetRecord")
 */
class MeetRecord {

    public function __construct($event, $recdate, $rectime, $rectext) {
        $this->event = $event;
        $this->recdate = $recdate;
        $this->rectime = $rectime;
        $this->rectext = $rectext;
    }

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="MeetEvent")
     * @ORM\JoinColumn(name="event_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $event;

    /**
     * @ORM\Column(type="date")
     */
    private $recdate;

    /**
     * @ORM\Column(type="time")
     */
    private $rectime;

    /**
     * @ORM\Column(type="string", length=512)
     */
    private $rectext;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getEvent() {
        return $this->event;
    }

    public function setEvent($event) {
        $this->event = $event;
    }

    public function getRecdate() {
        return $this->recdate;
    }

    public function setRecDate($recdate) {
        $this->recdate = $recdate;
    }

    public function getRectime() {
        return $this->rectime;
    }

    public function setRectime($rectime) {
        $this->rectime = $rectime;
    }

    public function getRectext() {
        return $this->rectext;
    }

    public function setRecText($rectext) {
        $this->rectext = $rectext;
    }
}

?>