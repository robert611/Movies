<?php

namespace App\Repository;

use App\Entity\UserWatchingHistory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method UserWatchingHistory|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserWatchingHistory|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserWatchingHistory[]    findAll()
 * @method UserWatchingHistory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserWatchingHistoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserWatchingHistory::class);
    }

    // /**
    //  * @return UserWatchingHistory[] Returns an array of UserWatchingHistory objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?UserWatchingHistory
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
