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

    public function findTopShows(int $showsLimit): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT * FROM show_ranking order by votes_up - votes_down DESC LIMIT ${showsLimit}";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
        } catch (DBALException $e) {
            return null;
        }

        return $stmt->fetchAll();
    }

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
