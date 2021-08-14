<?php 

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\UserWatchingHistory;

class UserWatchingHistoryService
{
    private $entityManager;
    private $userWatchingHistoryRepository;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->userWatchingHistoryRepository = $entityManager->getRepository(UserWatchingHistory::class);
    }

    public function saveVisitToUserWatchingHistory(\App\Entity\Shows $show, int $episodeId, ?\App\Entity\User $user): void
    {
        if ($user == null) return;

        $date = new \DateTime(date('Y-m-d H:i:s'));
        
        $showVisit = $this->userWatchingHistoryRepository->findOneBy(['series' => $show, 'user' => $user]);

        if ($showVisit == false)
        {
            $showVisit = new UserWatchingHistory();

            $showVisit->setUser($user);
            $showVisit->setSeries($show);
            $showVisit->setEpisodeId($episodeId);
            $showVisit->setDate($date);
        }
        else
        {
            $showVisit->setEpisodeId($episodeId);
            $showVisit->setDate($date);
        }

        $this->entityManager->persist($showVisit);
        $this->entityManager->flush();
    }
}