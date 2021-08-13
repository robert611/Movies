<?php 

namespace App\Service;

use App\Repository\ShowsRepository;
use App\Entity\Shows;

class FindSimilarShows
{
    private $showRepository;

    public function __construct(ShowsRepository $showRepository)
    {
        $this->showRepository = $showRepository;
    }

    public function getSimilarShows(Shows $showToCompare, $limit = 6): array
    {
        $shows = $this->showRepository->findAll();
        $showsWithAssignedPoints = [];

        foreach ($shows as $show)
        {
            if ($show->getId() == $showToCompare->getId()) continue;

            $similarityPoints = 0;

            if ($show->getCategory() == $showToCompare->getCategory()) $similarityPoints += 20;
            if ($show->getStudio() == $showToCompare->getStudio()) $similarityPoints += 5;

            foreach ($show->getThemes() as $showTheme)
            {
                if ($showToCompare->getThemes()->contains($showTheme)) $similarityPoints += 10;
            }

            if ($similarityPoints > 0) $showsWithAssignedPoints[] = ['show' => $show, 'similarity_points' => $similarityPoints];
        }

        array_multisort(array_column($showsWithAssignedPoints, 'similarity_points'), SORT_DESC, $showsWithAssignedPoints);

        return array_slice($showsWithAssignedPoints, 0, $limit);
    } 
}