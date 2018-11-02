<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AttendanceEntryRepository")
 * @ORM\Table(name="AttendanceEntry")
 */
class AttendanceEntry {

    public function __construct($athlete, $meet, $attending, $notes) {
        $this->athlete = $athlete;
        $this->meet = $meet;
        $this->attending = $attending;
        $this->notes = $notes;
    }

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
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
     * @ORM\Column(type="smallint")
     */
    private $attending;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $notes;

    public function getAthlete() {
        return $this->athlete;
    }

    public function setAthlete($athlete) {
        $this->athlete = $athlete;
    }

    public function getMeet() {
        return $this->meet;
    }

    public function setMeet($meet) {
        $this->meet = $meet;
    }

    public function getAttending() {
        return $this->attending;
    }

    public function setAttending($attending) {
        $this->attending = $attending;
    }

    public function getNotes() {
        return $this->notes;
    }

    public function setNotes($notes) {
        $this->notes = $notes;
    }
}

?>