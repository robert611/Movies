<?php

namespace App\Repository;

use App\Entity\ShowTheme;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShowTheme|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShowTheme|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShowTheme[]    findAll()
 * @method ShowTheme[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShowThemeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShowTheme::class);
    }

    // /**
    //  * @return ShowTheme[] Returns an array of ShowTheme objects
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
    public function findOneBySomeField($value): ?ShowTheme
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
