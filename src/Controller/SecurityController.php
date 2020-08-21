<?php

namespace App\Controller;

use Twig\Environment;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // if ($this->getUser()) {
        //     return $this->redirectToRoute('target_path');
        // }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
    /**
     * @Route("/confirm/{token}", name="app_confirm")
     */
    public function confirm(string $token, UserRepository $userRepository, EntityManagerInterface $em, Environment $twig)
    {
        $user = $userRepository->findOneBy(
            [
            'confirmationToken' => $token
            ]
        );
        if (null !== $user ){
            $user->setEnabled(true);
            $user->setConfirmationToken('');

            $em->flush();
        }
        return new Response(
            $twig->render(
                'security/confirmation.html.twig', [
            'user' => $user,
        ]
        ) 
    );
        
    }
    
}
