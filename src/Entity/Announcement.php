<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Announcement")
 */
class Announcement {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="text", length=128)
     */
    private $title;

    /**
     * @ORM\Column(type="text", length=1024)
     */
    private $atext;

    /**
     * @ORM\Column(type="date")
     */
    private $start;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $exp;

    public function getId() {
        return $this->id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function getAText() {
        return $this->atext;
    }

    public function setAText($atext) {
        $this->atext = $atext;
    }

    public function getStart() {
        return $this->start;
    }

    public function setStart($start) {
        $this->start = $start;
    }

    public function getExp() {
        return $this->exp;
    }

    public function setExp($exp) {
        $this->exp = $exp;
    }
}

?>