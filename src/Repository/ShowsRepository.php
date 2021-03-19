<?php

namespace App\Repository;

use App\Entity\Shows;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Shows|null find($id, $lockMode = null, $lockVersion = null)
 * @method Shows|null findOneBy(array $criteria, array $orderBy = null)
 * @method Shows[]    findAll()
 * @method Shows[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShowsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Shows::class);
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

    public function findShowAllEpisodesBy(string $showName, string $searchedTerm): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT * FROM ${showName} WHERE title LIKE :searched_term";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute(['searched_term' => '%'.$searchedTerm.'%']);
        } catch (DBALException $e) {
            return $e->getMessage();
        }

        return $stmt->fetchAll();
    }

    public function findEpisode(string $showTableName, int $episodeId)
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT * FROM {$showTableName} where id = :id";

        try {
            $stmt = $conn->prepare($sql);

            $stmt->execute(['id' => $episodeId]);
        } catch (DBALException $e) {
            return $e->getMessage();
        }

        return $stmt->fetch();
    }

    public function checkIfTableExists(string $tableName): bool
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT * FROM information_schema.tables WHERE table_schema = :table_schema 
            AND table_name = :table_name LIMIT 1";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute(['table_schema' => 'movies', 'table_name' => $tableName]);
        } catch (DBALException $e) {
            echo $e->getMessage();
        }

        if ($stmt->rowCount() > 0) {
            return true;
        }

        return false;
    }

    public function deleteShowDatabaseTable(string $tableName): bool
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "DROP table ${tableName}";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
        } catch (DBALException $e) {
            return $e->getMessage();
        }

        return true;
    }
}
