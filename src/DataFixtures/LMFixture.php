<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Team;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Asset\Package;
use Symfony\Component\Asset\VersionStrategy\EmptyVersionStrategy;
use Symfony\Component\HttpFoundation\File\File;

class LMFixture extends Fixture{

    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder) {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager){
        $user = new User();
        $user->setUsername('taylorsarnold.com');
        $user->setRoles(array('ROLE_ADMIN'));
        $password = $this->encoder->encodePassword($user, '#bluedolphins');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();

        $teams = array(
            new Team('LM', 'Lake Manassas', null),
            new Team('VOSD', 'Virginia Oaks', null),
            new Team('SPST', 'Stonewall', null),
            new Team('BLST', 'Ben Lomond', null),
            new Team('SBST', 'Southbridge', null),
            new Team('UST', 'Urbanna', null),
            new Team('BHST', 'Brookside', null),
        );
        for($i = 0; $i < count($teams); $i++){
            $manager->persist($teams[$i]);
            $manager->flush();
        }
    }
}

?>