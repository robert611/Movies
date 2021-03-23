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

    public function deleteShowEpisodeLinks(string $showDatabaseTableName, int $episodeId): bool
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "DELETE FROM show_links WHERE show_database_table_name = :show_database_table_name AND episode_id = :episode_id";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute(['show_database_table_name' => $showDatabaseTableName, 'episode_id' => $episodeId]);
        } catch (DBALException $e) {
            echo $e->getMessage();

            return false;
        }

        return true;
    }
}
