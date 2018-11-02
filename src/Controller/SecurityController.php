<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

use App\Entity\User;

class SecurityController extends Controller {

    /**
     * @Route("/admin/login_success", name="login_success")
     */
    public function admin(){
        return new JsonResponse(array('ADMIN_LOGIN_SUCCESS' => true));
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout(Request $request) {}

    /**
     * @Route("/login", name="login")
     */
    public function login(Request $request, AuthenticationUtils $authenticationUtils) {

        $errors = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login.html.twig', array(
            'errors' => $errors, 'last_username' => $lastUsername
        ));
    }
}

?>