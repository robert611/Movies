<?php 

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Visitor;
use App\Entity\PageVisitors;
use App\Entity\Shows;

class AdminVisitorService
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getPageVisitorsBy(?string $orderBy, object $date)
    {
        $pageVisitorsRepository = $this->entityManager->getRepository(PageVisitors::class);

        $pageVisitors = $pageVisitorsRepository->findBy(['date' => $date], ['displays' => 'DESC']);

        foreach ($pageVisitors as $pageVisitor)
        {
            $parameters = explode("/", $pageVisitor->getUrl());

            $pageVisitor->showName = isset($parameters[2]) ? $parameters[2] : null;
            $pageVisitor->episodeId = isset($parameters[4]) ? $parameters[4] : null;
        }

        if ($orderBy == 'allShowEpisodesDisplays')
        {
            $shows = $this->entityManager->getRepository(Shows::class)->findAll();

            $pageVisitors = array();

            foreach ($shows as $show)
            {
                $showSubpages = $pageVisitorsRepository->findByLike('url', $show->getDatabaseTableName(), $date->format('Y-m-d'));
    
                $combinedVisits = 0;
    
                foreach ($showSubpages as $subpage)
                {
                    $combinedVisits += $subpage['displays'];
                }
    
                $pageVisitors[] = ['show' => $show->getDatabaseTableName(), 'visits' => $combinedVisits];

                usort($pageVisitors, function ($a, $b) {
                    return $b['visits'] <=> $a['visits'];
                });
            }
        }

        return $pageVisitors;
    }
}