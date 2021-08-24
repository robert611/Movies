<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use App\Entity\Shows;

class ShowsFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $shows = $this->getShows();

        foreach ($shows as $key => $data) {
            $show = new Shows();

            $show->setName($data['name']);
            $show->setDatabaseTableName($data['database_table_name']);
            $show->setPicture($data['picture']);
            $show->setDescription('');
            $show->setCreatedAt(new \DateTime());
            $show->setUpdatedAt(new \DateTime());
            $show->setUser($this->getReference('user.' . ($data['user_id'] - 1)));
            $show->setCategory($this->getReference('show_category.' . ($data['show_category_id'] - 1)));
            $show->setStudio($this->getReference('studio.' . ($data['studio_id'] - 1)));

            $manager->persist($show);

            $this->addReference('show.' . $key, $show);
        }

        $manager->flush();
    }

    public function getShows(): array
    {
        return [
            ['name' => 'Burn Notice', 'database_table_name' => 'burn_notice', 'picture' => 'burn_notice.png', 'user_id' => 1, 'show_category_id' => 1, 'studio_id' => 1],
            ['name' => 'Lost', 'database_table_name' => 'lost', 'picture' => 'lost.png', 'user_id' => 1, 'show_category_id' => 6, 'studio_id' => 2],
            ['name' => 'Mr. Robot', 'database_table_name' => 'mr_robot', 'picture' => 'mr_robot.png', 'user_id' => 1, 'show_category_id' => 6, 'studio_id' => 1],
            ['name' => 'The House of Paper', 'database_table_name' => 'the_house_of_paper', 'picture' => 'the_house_of_paper.png', 'user_id' => 1, 'show_category_id' => 6, 'studio_id' => 3],
            ['name' => 'House of Cards', 'database_table_name' => 'house_of_cards', 'picture' => 'house_of_cards.png', 'user_id' => 1, 'show_category_id' => 6, 'studio_id' => 3],
        ];
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            ShowCategoryFixtures::class,
            StudioFixtures::class
        );
    }
}
