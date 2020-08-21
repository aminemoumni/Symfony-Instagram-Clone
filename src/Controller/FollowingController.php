<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Security("is_granted('ROLE_USER')")
 * @Route("/following")
 */
class FollowingController extends Controller
{
    /**
     * @Route("/follow/{id}", name="following_follow")
     */
    public function follow(User $userToFollow)
    {
        //getUser its a method from Controller not abstract to import the current user auhtenticated
        /** @var User $currentUser */
       $currentUser = $this->getUser();

       if($userToFollow->getId() !== $currentUser->getId()) { 
           // this if condition to check the user cant follow him self
           // $userToFollow is the persone who i want follow and $currentUseris the personne authenticated

            $currentUser->getFollowing()
                        ->add($userToFollow);

            $this->getDoctrine()
                ->getManager()
                ->flush();
       }
       
       return $this->redirectToRoute('micro_post_user', [
           'email' => $userToFollow->getEmail()
       ]);
    }
    /**
     * @Route("/unfollow/{id}", name="following_unfollow")
     */
    public function unfollow(User $userToUnfollow)
    {
        $currentUser = $this->getUser();
        $currentUser->getFollowing()
                     ->removeElement($userToUnfollow);
        $this->getDoctrine()
            ->getManager()
            ->flush();
        return $this->redirectToRoute('micro_post_user', [
            'email' => $userToUnfollow->getEmail()
        ]);
    }
}
