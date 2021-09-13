<?php 

namespace App\Service;

use App\Entity\ShowRanking;

class ShowRankingService 
{
    private array $ranking;

    public function __construct(array $ranking)
    {
        $this->ranking = $ranking;
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