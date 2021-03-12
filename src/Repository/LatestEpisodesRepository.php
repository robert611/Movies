<?php

namespace App\Repository;

use App\Entity\LatestEpisodes;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LatestEpisodes|null find($id, $lockMode = null, $lockVersion = null)
 * @method LatestEpisodes|null findOneBy(array $criteria, array $orderBy = null)
 * @method LatestEpisodes[]    findAll()
 * @method LatestEpisodes[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LatestEpisodesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LatestEpisodes::class);
    }

    // /**
    //  * @return LatestEpisodes[] Returns an array of LatestEpisodes objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?LatestEpisodes
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
