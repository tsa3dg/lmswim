<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="MeetEvent")
 */
class MeetEvent {

    public function __construct($id, $strokeName, $sex, $distance, $agegroup, $order) {
        $this->id = $id;
        $this->strokeName = $strokeName;
        $this->sex = $sex;
        $this->distance = $distance;
        $this->agegroup = $agegroup;
        $this->order = $order;
    }

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $strokeName;

    /**
     * @ORM\Column(type="smallint")
     */
    private $sex;

    /**
     * @ORM\Column(type="integer")
     */
    private $distance;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $agegroup;

    /**
     * @ORM\Column(type="integer")
     */
    private $order;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getStrokeName() {
        return $this->strokeName;
    }

    public function setStrokeName($strokeName) {
        $this->strokeName = $strokeName;
    }

    public function getSex() {
        return $this->sex;
    }

    public function setSex($sex) {
        return $this->sex = $sex;
    }

    public function getDistance() {
        return $this->distance = $distance;
    }

    public function setDistance($distance) {
        $this->distance = $distance;
    }

    public function getAgegroup() {
        return $this->agegroup;
    }

    public function setAgeGroup($agegroup) {
        return $this->agegroup = $agegroup;
    }

    public function getOrder() {
        return $this->order;
    }

    public function setOrder($order) {
        $this->order = $order;
    }
}

?>