<?php

namespace App\Repository;

use App\Entity\ShowCategory;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShowCategory|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShowCategory|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShowCategory[]    findAll()
 * @method ShowCategory[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShowCategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShowCategory::class);
    }

    // /**
    //  * @return ShowCategory[] Returns an array of ShowCategory objects
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
    public function findOneBySomeField($value): ?ShowCategory
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
