<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AthleteRepository")
 * @ORM\Table(name="Athlete")
 */
class Athlete {

    public function __construct($id, $first, $last, $birthday, $idNo, $sex, $team) {
        $this->id = $id;
        $this->first = $first;
        $this->last = $last;
        $this->birthday=  $birthday;
        $this->idNo = $idNo;
        $this->sex = $sex;
        $this->team = $team;
    }

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $first;

    /**
     * @ORM\Column(type="string", length=40)
     */
    private $last;

    /**
     * @ORM\Column(type="date")
     */
    private $birthday;

    /**
     * @ORM\Column(type="string", length=14)
     */
    private $idNo;

    /**
     * @ORM\Column(type="string")
     */
    private $sex;
    
    /**
     * @ORM\ManyToOne(targetEntity="Team")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     */
    private $team;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getFirst() {
        return $this->first;
    }

    public function setFirst($first) {
        $this->first = $first;
    } 

    public function getLast() {
        return $this->last;
    }

    public function setLast($last) {
        $this->last = $last;
    }

    public function getSex() {
        return $this->sex;
    }

    public function setSex($sex) {
        $this->sex = $sex;
    }

    public function getBirth() {
        return $this->birthday;
    }

    public function setBirth($birthday) {
        $this->birthday = $birthday;
    }

    public function getIdNo() {
        return $this->idNo;
    }

    public function setIdNo($idNo) {
        $this->idNo = $idNo;
    }

    public function getTeam() {
        return $this->team;
    }

    public function setTeam($team) {
        $this->team = $team;
    }

    public function getFullName() {
        return $this->first.' '.$this->last;
    }

    public function compare(Athlete $other) {
        return $this->first == $other->getFirst() &&
            $this->last == $other->getLast() &&
            $this->sex == $other->getSex() &&
            $this->birthday == $other->getBirth() &&
            $this->idNo == $other->getIdNo() &&
            $this->team == $other->getTeam();
    }
}

?>
