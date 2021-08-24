<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use App\Entity\UserWatchingHistory;

class UserWatchingHistoryFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $usersWatchingHistory = $this->getUsersWatchingHistory();

        foreach ($usersWatchingHistory as $key => $data) {
            $userWatchingHistory = new UserWatchingHistory();

            $userWatchingHistory->setUser($this->getReference('user.' . ($data['user_id'] - 1)));
            $userWatchingHistory->setSeries($this->getReference('show.' . ($data['show_id'] - 1)));
            $userWatchingHistory->setEpisodeId($data['episode_id']);
            $userWatchingHistory->setDate(new \DateTime());

            $manager->persist($userWatchingHistory);

            $this->addReference('userWatchingHistory.' . $key, $userWatchingHistory);
        }

        $manager->flush();
    }

    public function getUsersWatchingHistory(): array
    {
        return [
            ['user_id' => 1, 'show_id' => 1, 'episode_id' => 1],
            ['user_id' => 1, 'show_id' => 2, 'episode_id' => 1],
            ['user_id' => 1, 'show_id' => 3, 'episode_id' => 1],
            ['user_id' => 1, 'show_id' => 4, 'episode_id' => 1],
        ];
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            ShowsFixtures::class
        );
    }
}
