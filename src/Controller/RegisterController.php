<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\UserType;
use App\Event\UserRegisterEvent;
use App\Security\TokenGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class RegisterController extends AbstractController
{
    /**
     * @Route("/register", name="register")
     */
    public function register( Request $request, UserPasswordEncoderInterface $passEncoder, 
                                EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher,
                                TokenGenerator $tokenGenerator)
    {
       $user = new User();
       $form = $this->createForm(UserType::class, $user);
       $form->handleRequest($request);
       
       if($form->isSubmitted() && $form->isValid())
        {
            $user->setRoles([User::ROLE_USER]);
            $password = $passEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            //inserttoken for confirmation email 
            $user->setConfirmationToken($tokenGenerator->getRandomSecureToken(30));

            // end 
            
            $entityManager->persist($user);
            $entityManager->flush();

            // this event send an confirmation email 
            $userRegisterEvent = new UserRegisterEvent($user);
            $eventDispatcher->dispatch(UserRegisterEvent::NAME, $userRegisterEvent );
            // end confirmation 
            return $this->redirectToRoute('micro_post_index');
        }

        return $this->render('register/index.html.twig', [
            'form' => $form->createView()
        ]);
                                        

    }
}

 // $form = $this->createFormBuilder()
        //         ->add('fullname')
        //         ->add('email')
        //         ->add('password', RepeatedType::class, [
        //             'type' => PasswordType::class,
        //             'required' => true,
        //             'first_options' => ['label' => 'Password'],
        //             'second_options' => ['label' => 'Confirme Password']
        //         ])
        //         ->add('register', SubmitType::class)
        //         ->getForm();

        // $form->handleRequest($request);
        // if($form->isSubmitted() && $form->isValid())
        // {
        //     $data = $form->getData();
        //     $user = new User();
        //     $user->setFullname($data['fullname']);
        //     $user->setEmail($data['email']);
        //     $user->setPassword($passEncoder->encodePassword($user, $data['password']));

        //     $em = $this->getDoctrine()->getManager();
        //     $em->persist($user);
        //     $em->flush();
        //     return $this->redirect($this->generateUrl('app_login'));
        // }
    
        // return $this->render('register/index.html.twig', [
        //     'form' => $form->createView()
        // ]);