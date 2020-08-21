<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\MicroPost;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @Security("is_granted('ROLE_USER')")
 * @Route("/likes")
 */
class LikesController extends Controller
{
   /**
    * @Route("/like/{id}", name="likes_like")  
    */ 
   public function like(MicroPost $microPost)
   {
        $currentUser = $this->getUser();


        if (!$currentUser instanceof User){
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }
        
        //$microPost->getLikedBy()->add($currentUser);
        $microPost->like($currentUser);
        $this->getDoctrine()->getManager()->flush();

       return new JsonResponse([
           'count' => $microPost->getLikedBy()->count()
       ]);
   }
    /**
    * @Route("/unlike/{id}", name="likes_unlike")  
    */
   public function unlike(MicroPost $microPost)
   {
        $currentUser = $this->getUser();
        if (!$currentUser instanceof User){
            return new JsonResponse([], Response::HTTP_UNAUTHORIZED);
        }

        $microPost->getLikedBy()->removeElement($currentUser);
        $this->getDoctrine()->getManager()->flush();

    
        return new JsonResponse([
            'count' => $microPost->getLikedBy()->count()
        ]);
   }
}
