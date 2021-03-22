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

    public function getLatestEpisodesWithFilledData(int $numberOfEpisodes = 9): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $latestEpisodes = $this->getLatestEpisodes($numberOfEpisodes);

        $latestEpisodesWithFilledData = array();

        foreach ($latestEpisodes as $episode)
        {
            $sql = "SELECT e.id, e.title, e.season, s.picture, s.name as show_name, s.database_table_name, e.created_at FROM latest_episodes le, shows s, {$episode['show_database_table_name']} e WHERE s.database_table_name = :table_name AND le.show_database_table_name = :table_name AND e.id = :episode_id";

            try {
                $stmt = $conn->prepare($sql);
                $stmt->execute(['table_name' => $episode['show_database_table_name'], 'episode_id' => $episode['episode_id']]);
            } catch (DBALException $e) {
                return $e->getMessage();
            }

            $latestEpisodesWithFilledData[] = $stmt->fetch();
        }

        return $latestEpisodesWithFilledData;
    }

    public function getLatestEpisodes(int $numberOfEpisodes): array
    {
        $conn = $this->getEntityManager()->getConnection();

        $sql = "SELECT * FROM latest_episodes ORDER BY id DESC LIMIT ${numberOfEpisodes}";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->execute();
        } catch (DBALException $e) {
            return $e->getMessage();
        }

        return $stmt->fetchAll();
    }
}
