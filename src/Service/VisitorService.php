<?php 

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Visitor;
use App\Entity\PageVisitors;

class VisitorService 
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function addVisit(): void
    {
        $date = new \DateTime(date('Y-m-d'));
        
        $visitorRepository = $this->entityManager->getRepository(Visitor::class);

        $todayVisitors = $visitorRepository->findOneBy(['date' => $date]);

        if ($todayVisitors == false) 
        {
            $todayVisitors = new Visitor();

            $todayVisitors->setDisplays(1);
            $todayVisitors->setVisitors(1);
            $todayVisitors->setDate($date);

            $this->setCookie();
        }
        else 
        {
            if (!isset($_COOKIE['movies-visitor']))
            {
                $todayVisitors->setVisitors($todayVisitors->getVisitors() + 1);

                $this->setCookie();
            }

            $todayVisitors->setDisplays($todayVisitors->getDisplays() + 1);
        }

        $this->entityManager->persist($todayVisitors);
        $this->entityManager->flush();
    }

    public function addPageVisit(string $url): void
    {
        $date = new \DateTime(date('Y-m-d'));

        $pageVisitorsRepository = $this->entityManager->getRepository(PageVisitors::class);

        $todayPageVisitors = $pageVisitorsRepository->findOneBy(['date' => $date, 'url' => $url]);

        if ($todayPageVisitors == false)
        {
            $todayPageVisitors = new PageVisitors();

            $todayPageVisitors->setDisplays(1);
            $todayPageVisitors->setUrl($url);
            $todayPageVisitors->setDate($date);
        }
        else 
        {
            $todayPageVisitors->setDisplays($todayPageVisitors->getDisplays() + 1);
        }

        $this->entityManager->persist($todayPageVisitors);
        $this->entityManager->flush();
    }

    private function setCookie(): void
    {
        setcookie('movies-visitor', 1, time() + (86400 * 30), "/");
    }
}
