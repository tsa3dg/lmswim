<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use \Swift_Mailer;

class UserController extends Controller {

    /**
     * @Route("/superadmin/users", name="users_list")
     */
    public function listUsers() {
        $repo = $this->getDoctrine()->getRepository(User::class);
        $users = $repo->findAll();
        return $this->render('users.html.twig', array(
            'users' => $users,
        ));
    }

    /**
     * @Route("/superadmin/users/update", name="update_users_noid")
     * @Route("/superadmin/users/update/{id}", name="update_users")
     */
    public function updateUsers($id = -1, Request $request, UserPasswordEncoderInterface $encoder, Swift_Mailer $mailer) {
        $em = $this->getDoctrine()->getManager();
        $repo = $this->getDoctrine()->getRepository(User::class);
        if($id == -1) {
            $user = new User();
        }else {
            $user = $repo->getOneById($id);
        }

        $form = $this->createFormBuilder($user)
            ->setAction($request->getUri())
            ->add('username', EmailType::class, array(
                'required' => ($id == -1),
                'disabled' => !($id == -1),
            ))
            ->add('plainPassword', RepeatedType::class, array(
                'type' => PasswordType::class,
                'first_options' => array('label', 'Password'),
                'second_options' => array('label', 'Repeat Password'),
                'required' => ($id == -1),
                'disabled' => !($id == -1),
            ))
            ->add('roles', ChoiceType::class, array(
                'choices' => array(
                    'Admin' => 'SUPERADMIN_ROLE',
                    'Coach' => 'ADMIN_ROLE',
                )
            ))
            ->getForm();

        $form->handleRequest($request);
        if($form->isSubmitted()) {
            if($form->isValid()) {             
                $password = $encoder->encodePassword($user, $user->getPlainPassword());
                $user->setPassword($password);

                if($id == -1) {
                    $em->persist($user);
                    $notification = (new Swift_Mailer('You have been given access!'))
                        ->setTo($user->getUsername())
                        ->setBody(
                            $this->renderView(
                                'emails/registration.html.twig',
                                array(
                                    'email' => $user->getUsername(),
                                    'plainPassword' => $user->getPlainPassword,
                                    'role' => $user->getRoles[0],
                                )
                            ),
                            'text/html'
                        );
                }else {

                }
                $em->flush();
            }
        }

        return $this->render('form.html.twig', array(
            'form' => $form->createView(),
        ));
    }

}

?>