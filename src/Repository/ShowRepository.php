<?php

namespace App\Repository;

use App\Entity\Show;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Shows|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shows|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shows[]    findAll()
 * @method Shows[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Show::class);
    }

    public function createShowTable(string $tableName): bool
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "CREATE TABLE {$tableName} (id INT PRIMARY KEY AUTO_INCREMENT, title VARCHAR(86) NOT NULL, season SMALLINT, episode SMALLINT, description VARCHAR(1024)
            DEFAULT NULL, user_id INT, created_at DATETIME, updated_at DATETIME)";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
        } catch (DBALException $e) {
            return $e->getMessage();
        }

        return true;
    }
}
