<?php 

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ShowRanking;

class ShowRankingService 
{
    private EntityManagerInterface $entityManager;
    private array $ranking;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->ranking = $this->entityManager->getRepository(ShowRanking::class)->getTopShows(15);
    }

    public function getPosition(string $showTableName): null | int
    {
        foreach ($this->ranking as $key => $show)
        {
            if ($show['show_database_table_name'] == $showTableName)
            {
                return $key + 1;
            }
        }

        return null;
    }
}