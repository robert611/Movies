<?php

namespace App\Repository;

use App\Entity\PageVisitors;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PageVisitors|null find($id, $lockMode = null, $lockVersion = null)
 * @method PageVisitors|null findOneBy(array $criteria, array $orderBy = null)
 * @method PageVisitors[]    findAll()
 * @method PageVisitors[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageVisitorsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PageVisitors::class);
    }

    public function findByLike(string $column, string $keyword, string $date): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT * FROM page_visitors WHERE ${column} LIKE :keyword AND date = :date";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute(['keyword' => '%'.$keyword.'%', 'date' => $date]);
        } catch (DBALException $e) {
            return $e->getMessage();
        }

        return $stmt->fetchAll();
    }
}
