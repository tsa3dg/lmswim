<?php

namespace App\Entity;

use App\Entity\Meet;
use Doctrine\ORM\Mapping as ORM;
use \DateTime;
use \DateInterval;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ExternalEventRepository")
 * @ORM\Table(name="ExternalEvent")
 */
class ExternalEvent {

    public const REPEAT_NONE = 0;
    public const REPEAT_DAILY = 1;
    public const REPEAT_WEEKLY = 2;
    public const REPEAT_MONTHLY = 3;
    public const REPEAT_YEARLY = 4;

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=96)
     */
    private $title;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $end;

    /**
     * @ORM\Column(type="smallint", name="repeat_for")
     */
    private $repeat;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $endRepeat;

    /**
     * @ORM\Column(type="string", length=96)
     */
    private $location;

    /**
     * @ORM\Column(type="string", name="description", length=512, nullable=true)
     */
    private $desc;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isHoliday;

    /**
     * @ORM\OneToOne(targetEntity="Meet")
     * @ORM\JoinColumn(name="meet_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $linkedMeet;

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($title) {
        $this->title = $title;
    }

    public function isAllDay() {
        return $this->end == null || ($this->start && 
            $this->start->format('H:i') == '00:00' &&
            $this->start->format('H:i') == '23:59');
    }

    public function setAllDay() {
        $this->start->setTime(0, 0);
        $this->end = null;
    }

    public function getStart() {
        return $this->start;
    }

    public function setStart($start) {
        $this->start = $start;
    }

    public function getEnd() {
        return $this->end;
    }

    public function setEnd($end) {
        $this->end = $end;
    }

    public function hasEnd() {
        return $this->end != null && $this->end->format('H:i') != '23:59';
    }

    public function getLocation() {
        return $this->location;
    }

    public function setLocation($location) {
        $this->location = $location;
    }

    public function getDesc() {
        return $this->desc;
    }

    public function setDesc($desc) {
        $this->desc = $desc;
    }

    public function getRepeat() {
        return $this->repeat;
    }

    public function setRepeat($repeat) {
        $this->repeat = $repeat;
    }

    public function getEndRepeat() {
        return $this->endRepeat;
    }

    public function setEndRepeat($endRepeat) {
        $this->endRepeat = $endRepeat;
    }

    public function getIsHoliday() {
        return $this->isHoliday;
    }

    public function setIsHoliday($isHoliday) {
        $this->isHoliday = $isHoliday;
    }

    public function getLinkedMeet() {
        return $this->linkedMeet;
    }

    public function setLinkedMeet(Meet $linkedMeet) {
        $this->linkedMeet = $linkedMeet;
    }

    public function toArray() {
        return array(
            'id' => $this->id,
            'title' => $this->title,
            'start' => $this->start,
            'end' => $this->end,
            'location' => $this->location,
            'desc' => $this->desc,
            'repeat' => $this->repeat,
            'endRepeat' => $this->endRepeat,
        );
    }
}

?>