<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MeetRepository")
 * @ORM\Table(name="Meet")
 */
class Meet {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumn(name="team1", referencedColumnName="id")
     */
    private $team1;

    /**
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumn(name="team2", referencedColumnName="id")
     */
    private $team2;

    /**
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumn(name="team3", referencedColumnName="id", nullable=true)
     */
    private $team3;

    /**
     * @ORM\Column(type="date")
     */
    private $start;

    /**
     * @ORM\Column(type="date")
     */
    private $ageup;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $resultsName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $meetTimelineName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $meetProgramName;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $scoresName;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getTeam1() {
        return $this->team1;
    }

    public function setTeam1($team1) {
        $this->team1 = $team1;
    }

    public function getTeam2() {
        return $this->team2;
    }

    public function setTeam2($team2) {
        $this->team2 = $team2;
    }

    public function getTeam3() {
        return $this->team3;
    }

    public function setTeam3($team3) {
        $this->team3 = $team3;
    }

    public function getStart() {
        return $this->start;
    }

    public function setStart($start) {
        $this->start = $start;        
    }

    public function getAgeUp() {
        return $this->ageup;
    }

    public function setAgeUp($ageup) {
        $this->ageup = $ageup;
    }

    public function getResultsName() {
        return $this->resultsName;
    }

    public function setResultsName($resultsName) {
        $this->resultsName = $resultsName;
    }

    public function getMeetTimelineName() {
        return $this->meetTimelineName;
    }

    public function setMeetTimelineName($meetTimelineName) {
        $this->meetTimelineName = $meetTimelineName;
    }

    public function getMeetProgramName() {
        return $this->meetProgramName;
    }

    public function setMeetProgramName($meetProgramName) {
        $this->meetProgramName = $meetProgramName;
    }

    public function getScoresName() {
        return $this->scoresName;
    }

    public function setScoresName($scoresName) {
        $this->scoresName = $scoresName;
    }

    public function getShortTitle() {
        if($this->isDivisionals()) {
            return 'Divisionals @ '.$this->team1->getCode();
        } else {
            return $this->team2->getCode().' @ '.$this->team1->getCode();
        }
    }

    public function isDivisionals() {
        return $this->team3 != null;
    }
}

?>