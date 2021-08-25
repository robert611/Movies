<?php 

namespace App\Service;

use App\Repository\ShowsRepository;
use App\Entity\Shows;

class FindSimilarShows
{
    const CATEGORY_POINTS = 20;
    const THEME_POINTS = 10;
    const STUDIO_POINTS = 5;

    private $showRepository;

    public function __construct(ShowsRepository $showRepository)
    {
        $this->showRepository = $showRepository;
    }

    public function getSimilarShows(Shows $showToCompare, $limit = 6): array
    {
        $shows = $this->showRepository->findAll();

        $showsWithAssignedPoints = $this->evaluateShowsSimilarity($showToCompare, $shows);

        $showsSortedBySimilarityPoints = $this->sortShowsBySimilarityPoints($showsWithAssignedPoints);

        return array_slice($showsSortedBySimilarityPoints, 0, $limit);
    }

    public function evaluateShowsSimilarity(Shows $showToCompare, array $shows): array
    {
        $showsWithAssignedPoints = [];

        foreach ($shows as $show)
        {
            if ($show->getId() == $showToCompare->getId()) continue;

            $similarityPoints = 0;

            if ($show->getCategory() == $showToCompare->getCategory()) $similarityPoints += self::CATEGORY_POINTS;
            if ($show->getStudio() == $showToCompare->getStudio()) $similarityPoints += self::STUDIO_POINTS;

            foreach ($show->getThemes() as $showTheme)
            {
                if ($showToCompare->isInThemes($showTheme)) $similarityPoints += self::THEME_POINTS;
            }

            if ($similarityPoints > 0) $showsWithAssignedPoints[] = ['similarity_points' => $similarityPoints, 'show' => $show];
        }

        return $showsWithAssignedPoints;
    }

    public function sortShowsBySimilarityPoints($showsWithAssignedPoints): Array
    {
        usort($showsWithAssignedPoints, function ($a, $b) {
            return $b['similarity_points']- $a['similarity_points'];
        });

        return $showsWithAssignedPoints;
    }
}