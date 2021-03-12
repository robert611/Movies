<?php

namespace App\Repository;

use App\Entity\ShowLinks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShowLinks|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShowLinks|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShowLinks[]    findAll()
 * @method ShowLinks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShowLinksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShowLinks::class);
    }

    // /**
    //  * @return ShowLinks[] Returns an array of ShowLinks objects
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
    public function findOneBySomeField($value): ?ShowLinks
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
