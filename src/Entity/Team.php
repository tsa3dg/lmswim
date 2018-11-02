<?php

namespace App\Entity;

use Symfony\Component\HttpFoundation\File\File;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="Team")
 */
class Team {
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=10, unique=true)
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=60)
     */
    private $name;

    /**
     * @ORM\Column(type="blob", nullable=true)
     */
    private $logo;

    public function __construct($code, $name, $logo) {
        $this->code = $code;
        $this->name = $name;
        $this->logo = $logo;
    }

    public function getId() {
        return $this->id;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function getCode() {
        return $this->code;
    } 

    public function setCode($code) {
        $this->code = $code;
    }

    public function getName() {
        return $this->name;
    }

    public function setName($name) {
        $this->name = $name;
    }

    public function getLogo() {
        return $this->logo;
    }

    public function setLogo(File $logo) {
        $this->logo = $logo;
    }
}

?>