<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="LMUsers")
 */
class User implements UserInterface, \Serializable {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=191, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email(
     *      message="The email '{{ valid }}' is not a valid email.",
     *      checkMX=true
     * )
     */
    private $email;
    
    /**
     * @Assert\NotBlank()
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="string", length=64)
     */
    private $password;

    /**
     * @ORM\Column(type="array")
     * @Assert\Choice(choices={"SUPERADMIN_ROLE", "ADMIN_ROLE", "PARENT"})
     */
    private $roles;

    public function getUsername() {
        return $this->email;
    }

    public function setUsername($username) {
        $this->email = $username;
    }

    public function getPlainPassword() {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword) {
        $this->plainPassword = $plainPassword;
    }

    public function getPassword() {
        return $this->password;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function getRoles() {
        return $this->roles;
    }

    public function setRoles(array $roles) {
        $this->roles = $roles;
    }

    public function getSalt() {
        return null;
    }

    public function eraseCredentials() {}

    public function serialize() {
        return serialize(array(
            $this->id,
            $this->email,
            $this->password,
        ));
    }

    public function unserialize($serialized) {
        list(
            $this->id,
            $this->email,
            $this->password,
        ) = unserialize($serialized, ['allowed_classes' => false]);
    }
}

?>
