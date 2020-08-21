<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\MicroPost;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }
    public function load(ObjectManager $manager)
    {
        $this->loadUsers($manager);
        $this->loadMicroPost($manager);
        
    }

    private function loadMicroPost(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);
        for($i=0; $i < 10; $i++){
            $microPost = new MicroPost();
            $microPost->setText('Some random text' . rand(0,500));
            $microPost->setTime(new \Datetime('2020-03-09'));
            $microPost->setUser($this->getReference('amine_moumni'));
            $manager->persist($microPost);
        }

        $manager->flush();  
    }
    
    private function loadUsers(ObjectManager $manager)
    {
        $user = new User();
        $user->setFullname('amine moumni');
        $user->setEmail('childeroe12@gmail.com');
        $user->setPassword($this->passwordEncoder->encodePassword($user, '20121998'));

        $this->addReference('amine_moumni', $user);
        $manager->persist($user);
        $manager->flush();
    }

}
