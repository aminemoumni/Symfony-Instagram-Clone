<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\MicroPost;
use App\Form\MicroPostType;
use App\Repository\UserRepository;
use App\Repository\MicroPostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @Route("micro")
 */
class MicroPostController extends AbstractController
{
    private $microPostRepository;
    public function __construct( 
        MicroPostRepository $microPostRepository, //for connecte to database 
        FormFactoryInterface $formFactory, // to create form
        EntityManagerInterface $entityManager, //entity manager add/delete/edit...
        RouterInterface $router, // to new redirect to new route
        AuthorizationCheckerInterface $authorizationChecker // bach ikhelik tediti ola tsupprimi ghir les posts dyawlek
        
        )
    {
        $this->microPostRepository = $microPostRepository;
        $this->formFactory = $formFactory;
        $this->entityManager = $entityManager;
        $this->router = $router;
        $this->authrizationChecker = $authorizationChecker;
        
    }
    /**
     * @Route("/", name="micro_post_index")
     */
    public function index(TokenStorageInterface $token,  UserRepository $userRepository)
    {
        $currentUser = $token->getToken()->getUser();
        $usersToFollow = [];
        if($currentUser instanceof User){ // current user sho only the posts who follow
            $posts =  $this->microPostRepository->findAllByUsers($currentUser->getFollowing());
            
            $usersToFollow = count($posts) === 0 ?
            $userRepository->findAllWithMoreThan5PostsExceptUser(
                $currentUser
            ) : [];
        } else { // this for anonymouce users 
            $posts =  $this->microPostRepository->findby([], ['time' => 'DESC']);
            // $sql="select * from micro_post"; 
            // $stmt = $this->getDoctrine()->getConnection()->prepare($sql);
            // $stmt->execute();
            // $posts = $stmt->fetchAll();
        }
        return $this->render('micro_post/index.html.twig', [
           'posts' => $posts,
           'usersToFollow' => $usersToFollow,
        ]);
    }

    /**
     * @Route("/add", name="micro_post_add")
     * @Security("is_granted('ROLE_USER')")
     */
    public function add(Request $request, TokenStorageInterface $token)
    {
        $user = $token->getToken()->getUser();
        $microPost = new MicroPost();
        //$microPost->setTime(new \DateTime()); this metod is manually go check on MicroPost setTimeOnPrePersist
        $microPost->setUser($user); 
        
        $form = $this->formFactory->create(MicroPostType::class, $microPost);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->entityManager->persist($microPost);
            $this->entityManager->flush();
            $this->addFlash('notice', 'Micro post was created');

            return new RedirectResponse($this->router->generate('micro_post_index')); 
        }


        return $this->render('micro_post/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/edit/{id}", name="micro_post_edit")
     * @Security("is_granted('Edit', microPost)", message="Access denied")
     */
    public function edit(MicroPost $microPost, Request $request)
    {

        //if (!$this->authrizationChecker->isGranted('Edit', $microPost)){
            //throw new UnauthorizedHttpException(); // ila mabghitich lfo9aniya fl annotation kayna 7ta hadi 
       // }
        $form = $this->formFactory->create(MicroPostType::class, $microPost);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $this->entityManager->flush();
            return new RedirectResponse($this->router->generate('micro_post_index')); 
        }


        return $this->render('micro_post/add.html.twig', [
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/delete/{id}", name="micro_post_delete")
     * @Security("is_granted('Delete', microPost)", message="Access denied") 
     * 
     */
    public function delete(MicroPost $microPost, Request $request)
    {
        $this->entityManager->remove($microPost);
        $this->entityManager->flush();
        $this->addFlash('notice', 'Micro post was deleted');
        return new RedirectResponse($this->router->generate('micro_post_index'));
    }
    /**
     * @Route("/user/{email}", name="micro_post_user")
     */
    public function userPosts(User $userwithposts)
    {
        return $this->render('micro_post/user-posts.html.twig', [
           'posts' => $this->microPostRepository->findby(
               ['user' => $userwithposts], 
               ['time' => 'DESC']),
            'user' => $userwithposts,

            //'posts' => $userwithposts->getPosts()
        ]);
    }
    /**
     * @Route("/{id}", name="micro_post_post")
     */
    public function post($id)
     {
        $post = $this->microPostRepository->find($id);
        return $this->render('micro_post/post.html.twig', [
            'post' => $post
        ]);
    }
}
