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
     */
    private $id;

    /**
     * @ORM\Column(type="text", length=128)
     */
    private $title;

    /**
     * @ORM\Column(type="text", length=1024)
     */
    private $description;

    /**
     * @ORM\Column(type="date")
     */
    private $postDate;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $expirationDate;

    public function __construct($id, $title, $description, $postDate, $expirationDate) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->postDate = $postDate;
        $this->expirationDate = $expirationDate;
    }

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

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function getPostDate() {
        return $this->postDate;
    }

    public function setPostDate($postDate) {
        $this->postDate = $postDate;
    }

    public function getExpirationDate() {
        return $this->expirationDate;
    }

    public function setExpirationDate($expirationDate) {
        $this->expirationDate = $expirationDate;
    }
}

?>
