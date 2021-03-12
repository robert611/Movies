<?php

namespace App\Repository;

use App\Entity\ShowRanking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShowRanking|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShowRanking|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShowRanking[]    findAll()
 * @method ShowRanking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShowRankingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShowRanking::class);
    }

    // /**
    //  * @return ShowRanking[] Returns an array of ShowRanking objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ShowRanking
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
