<?php

namespace App\Repository;

use App\Entity\MicroPost;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Run|null find($id, $lockMode = null, $lockVersion = null)
 * @method Run|null findOneBy(array $criteria, array $orderBy = null)
 * @method Run[]    findAll()
 * @method Run[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MicroPostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MicroPost::class);
    }

    public function findAllByUsers(Collection $users) {
        $qb = $this->createQueryBuilder('p');
        return $qb->select('p')
        ->where('p.user IN (:following)')        
        ->setParameter('following', $users)
        ->orderBy('p.time', 'DESC')
        ->getQuery()
        ->getResult();
    }
}
