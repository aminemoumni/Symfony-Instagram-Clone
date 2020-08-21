<?php

namespace App\Security\Voter;

use App\Entity\User;
use App\Entity\MicroPost;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

class MicroPostVoter extends Voter
{
    const EDIT = "Edit";
    const DELETE = "Delete";

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }
    protected function supports($attribute, $subject)
    {
        if(!in_array($attribute, [self::EDIT, self::DELETE])){
            return false;
        }
        if(!$subject instanceof MicroPost){
            return false;
        }
        return true;
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        //return in_array($attribute, ['POST_EDIT', 'POST_VIEW'])
           // && $subject instanceof \App\Entity\BlogPost;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        if($this->decisionManager->decide($token, [User::ROLE_ADMIN])) {
            return true;
        }
        
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof User) {
            return false;
        }
        $microPost = $subject;
        return $microPost->getUser()->getId() === $user->getId();
        
         
    }
}
