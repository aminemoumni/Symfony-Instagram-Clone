<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
/**
 * @ORM\Entity(repositoryClass="App\Repository\MicroPostRepository")
 * @ORM\Table()
 * @ORM\HasLifecycleCallbacks()
 */
class MicroPost 
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn()
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=250)
     * @Assert\NotBlank()
     * @Assert\Length(min=3)
     */
    private $text;
    /**
     *@ORM\Column(type="datetime")
     */
    private $time;
    
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\User", inversedBy="postLiked")
     * @ORM\JoinTable(name="post_likes",
     *      joinColumns={@ORM\joinColumn(name="post_id", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\joinColumn(name="user_id", referencedColumnName="id")}
     * )
     */
    private $likedBy;


    public function __construct()
    {
        $this->likedBy = new ArrayCollection();
    }

    /**
     * Get the value of id
     */ 
    public function getId()
    {
        return $this->id;
        
    }


    /**
     * Get the value of text
     */ 
    public function getText()
    {
        return $this->text;
    }

   
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    
    public function getTime()
    {
        return $this->time;
    }

    
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }
    /**
     * @ORM\PrePersist()
     */
    public function setTimeOnPersist(): void
    {
        $this->time = new \Datetime();

    }
    /**
     * Get the value of user
     */ 
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set the value of user
     */ 
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection
     */ 
    public function getLikedBy()
    {
        return $this->likedBy;
    }
    public function like(User $user)
    {
        if($this->likedBy->contains($user)){
            return;
        }
        $this->likedBy->add($user);
    }
}
