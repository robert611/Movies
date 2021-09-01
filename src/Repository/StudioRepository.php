<?php

namespace App\Repository;

use App\Entity\Studio;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Studio|null find($id, $lockMode = null, $lockVersion = null)
 * @method Studio|null findOneBy(array $criteria, array $orderBy = null)
 * @method Studio[]    findAll()
 * @method Studio[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Studio::class);
    }

    public function findStudiosContainingShows(): array | bool
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT s.id, s.name FROM studio s where id IN(SELECT DISTINCT studio_id FROM shows)";

        try {
            $stmt = $conn->prepare($sql);

            $stmt->execute();
        } catch (DBALException $e) {
            return $e->getMessage();
        }

        return $stmt->fetchAll();
    }
}
